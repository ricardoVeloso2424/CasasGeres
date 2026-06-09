<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalendarSource;
use App\Models\RentalUnit;
use App\Services\CalendarSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarSourceController extends Controller
{
    public const PLATFORMS = [
        'Booking',
        'Airbnb',
        'Vrbo',
        'Manual',
        'Outro',
    ];

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $rentalUnitId = $request->query('rental_unit_id');
        $active = $request->query('active');

        $calendarSources = CalendarSource::query()
            ->with(['rentalUnit.house'])
            ->withCount('blockedDates')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('platform', 'like', "%{$search}%")
                        ->orWhere('ical_url', 'like', "%{$search}%")
                        ->orWhereHas('rentalUnit', function ($query) use ($search): void {
                            $query
                                ->where('name', 'like', "%{$search}%")
                                ->orWhereHas('house', fn ($query) => $query->where('name', 'like', "%{$search}%"));
                        });
                });
            })
            ->when($rentalUnitId, fn ($query) => $query->where('rental_unit_id', $rentalUnitId))
            ->when($active === '1', fn ($query) => $query->where('is_active', true))
            ->when($active === '0', fn ($query) => $query->where('is_active', false))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.calendar-sources.index', [
            'calendarSources' => $calendarSources,
            'rentalUnits' => $this->rentalUnitOptions(),
            'search' => $search,
            'rentalUnitId' => $rentalUnitId,
            'active' => $active,
        ]);
    }

    public function create(): View
    {
        return view('admin.calendar-sources.create-or-update', [
            'calendarSource' => new CalendarSource([
                'is_active' => true,
            ]),
            'rentalUnits' => $this->rentalUnitOptions(),
            'platforms' => self::PLATFORMS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $calendarSource = CalendarSource::query()->create($this->validatedData($request));

        return redirect()
            ->route('admin.calendar-sources.edit', $calendarSource)
            ->with('status', 'Fonte de calendario criada com sucesso.');
    }

    public function edit(CalendarSource $calendarSource): View
    {
        $calendarSource->load('rentalUnit.house');

        return view('admin.calendar-sources.create-or-update', [
            'calendarSource' => $calendarSource,
            'rentalUnits' => $this->rentalUnitOptions(),
            'platforms' => self::PLATFORMS,
        ]);
    }

    public function update(Request $request, CalendarSource $calendarSource): RedirectResponse
    {
        $calendarSource->update($this->validatedData($request));

        return redirect()
            ->route('admin.calendar-sources.edit', $calendarSource)
            ->with('status', 'Fonte de calendario atualizada com sucesso.');
    }

    public function destroy(CalendarSource $calendarSource): RedirectResponse
    {
        if ($calendarSource->blockedDates()->exists()) {
            return back()->with('error', 'Nao e possivel apagar uma fonte de calendario com datas bloqueadas associadas.');
        }

        $calendarSource->delete();

        return redirect()
            ->route('admin.calendar-sources.index')
            ->with('status', 'Fonte de calendario apagada com sucesso.');
    }

    public function sync(CalendarSource $calendarSource, CalendarSyncService $calendarSyncService): RedirectResponse
    {
        $result = $calendarSyncService->sync($calendarSource);

        if (! $result['success']) {
            return back()->with('error', "Sincronizacao falhou: {$result['error']}");
        }

        return back()->with(
            'status',
            "Fonte sincronizada com sucesso. Criados: {$result['created']}; atualizados: {$result['updated']}; removidos: {$result['deleted']}; ignorados: {$result['skipped']}."
        );
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'rental_unit_id' => ['required', 'integer', 'exists:rental_units,id'],
            'platform' => ['required', 'string', 'max:100'],
            'ical_url' => ['required', 'url', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        return [
            'rental_unit_id' => $data['rental_unit_id'],
            'platform' => $data['platform'],
            'ical_url' => $data['ical_url'],
            'is_active' => $request->boolean('is_active'),
        ];
    }

    private function rentalUnitOptions()
    {
        return RentalUnit::query()
            ->with('house')
            ->orderBy('name')
            ->get();
    }
}
