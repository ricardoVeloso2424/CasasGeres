<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedDate;
use App\Models\CalendarSource;
use App\Models\RentalUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class BlockedDateController extends Controller
{
    public const SOURCES = [
        'Manual',
        'Direct',
        'Booking',
        'Airbnb',
        'Vrbo',
        'Outro',
    ];

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $rentalUnitId = $request->query('rental_unit_id');
        $source = $request->query('source');
        $scope = $request->query('scope', 'future');

        $blockedDates = BlockedDate::query()
            ->with(['rentalUnit.house', 'calendarSource'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('source', 'like', "%{$search}%")
                        ->orWhere('summary', 'like', "%{$search}%")
                        ->orWhere('external_uid', 'like', "%{$search}%")
                        ->orWhereHas('rentalUnit', function ($query) use ($search): void {
                            $query
                                ->where('name', 'like', "%{$search}%")
                                ->orWhereHas('house', fn ($query) => $query->where('name', 'like', "%{$search}%"));
                        });
                });
            })
            ->when($rentalUnitId, fn ($query) => $query->where('rental_unit_id', $rentalUnitId))
            ->when($source, fn ($query) => $query->where('source', $source))
            ->when($scope !== 'all', fn ($query) => $query->where('ends_at', '>=', today()->toDateString()))
            ->orderBy('starts_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.blocked-dates.index', [
            'blockedDates' => $blockedDates,
            'rentalUnits' => $this->rentalUnitOptions(),
            'sources' => self::SOURCES,
            'search' => $search,
            'rentalUnitId' => $rentalUnitId,
            'source' => $source,
            'scope' => $scope,
        ]);
    }

    public function create(): View
    {
        return view('admin.blocked-dates.create-or-update', [
            'blockedDate' => new BlockedDate([
                'source' => 'Manual',
            ]),
            'rentalUnits' => $this->rentalUnitOptions(),
            'calendarSources' => $this->calendarSourceOptions(),
            'sources' => self::SOURCES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $blockedDate = BlockedDate::query()->create($this->validatedData($request));

        return redirect()
            ->route('admin.blocked-dates.edit', $blockedDate)
            ->with('status', 'Data bloqueada criada com sucesso.');
    }

    public function edit(BlockedDate $blockedDate): View
    {
        $blockedDate->load(['rentalUnit.house', 'calendarSource']);

        return view('admin.blocked-dates.create-or-update', [
            'blockedDate' => $blockedDate,
            'rentalUnits' => $this->rentalUnitOptions(),
            'calendarSources' => $this->calendarSourceOptions(),
            'sources' => self::SOURCES,
        ]);
    }

    public function update(Request $request, BlockedDate $blockedDate): RedirectResponse
    {
        $blockedDate->update($this->validatedData($request, $blockedDate));

        return redirect()
            ->route('admin.blocked-dates.edit', $blockedDate)
            ->with('status', 'Data bloqueada atualizada com sucesso.');
    }

    public function destroy(BlockedDate $blockedDate): RedirectResponse
    {
        $blockedDate->delete();

        return redirect()
            ->route('admin.blocked-dates.index')
            ->with('status', 'Data bloqueada apagada com sucesso.');
    }

    private function validatedData(Request $request, ?BlockedDate $blockedDate = null): array
    {
        $validator = Validator::make($request->all(), [
            'rental_unit_id' => ['required', 'integer', 'exists:rental_units,id'],
            'calendar_source_id' => ['nullable', 'integer', 'exists:calendar_sources,id'],
            'source' => ['required', 'string', 'max:100'],
            'external_uid' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'summary' => ['nullable', 'string', 'max:500'],
        ]);

        $validator->after(function ($validator) use ($request, $blockedDate): void {
            if (
                $validator->errors()->has('rental_unit_id')
                || $validator->errors()->has('calendar_source_id')
            ) {
                return;
            }

            $calendarSourceId = $request->input('calendar_source_id');

            if ($calendarSourceId) {
                $calendarSource = CalendarSource::query()->find($calendarSourceId);

                if ($calendarSource && (string) $calendarSource->rental_unit_id !== (string) $request->input('rental_unit_id')) {
                    $validator->errors()->add('calendar_source_id', 'A fonte de calendario tem de pertencer a mesma unidade.');
                }
            }

            if (
                $validator->errors()->has('rental_unit_id')
                || $validator->errors()->has('starts_at')
                || $validator->errors()->has('ends_at')
            ) {
                return;
            }

            $startsAt = Carbon::parse($request->input('starts_at'))->startOfDay();
            $endsAt = Carbon::parse($request->input('ends_at'))->startOfDay();

            $overlapExists = BlockedDate::query()
                ->where('rental_unit_id', $request->input('rental_unit_id'))
                ->where('starts_at', '<', $endsAt)
                ->where('ends_at', '>', $startsAt)
                ->when($blockedDate?->exists, fn ($query) => $query->whereKeyNot($blockedDate->id))
                ->exists();

            if ($overlapExists) {
                $validator->errors()->add('starts_at', 'Ja existe uma data bloqueada que sobrepoe este intervalo para esta unidade.');
            }
        });

        $data = $validator->validate();

        return [
            'rental_unit_id' => $data['rental_unit_id'],
            'calendar_source_id' => $data['calendar_source_id'] ?? null,
            'source' => $data['source'],
            'external_uid' => $data['external_uid'] ?? null,
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'summary' => $data['summary'] ?? null,
        ];
    }

    private function rentalUnitOptions()
    {
        return RentalUnit::query()
            ->with('house')
            ->orderBy('name')
            ->get();
    }

    private function calendarSourceOptions()
    {
        return CalendarSource::query()
            ->with('rentalUnit.house')
            ->orderBy('platform')
            ->get();
    }
}
