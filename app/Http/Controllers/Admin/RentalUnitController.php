<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\House;
use App\Models\RentalUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RentalUnitController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $houseId = $request->query('house_id');

        $units = RentalUnit::query()
            ->with(['house', 'amenities'])
            ->withCount(['amenities', 'blockedDates', 'bookingRequests'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%")
                        ->orWhereHas('house', fn ($query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($houseId, fn ($query) => $query->where('house_id', $houseId))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.rental-units.index', [
            'units' => $units,
            'houses' => House::query()->orderBy('name')->get(),
            'search' => $search,
            'houseId' => $houseId,
        ]);
    }

    public function create(): View
    {
        return view('admin.rental-units.create-or-update', [
            'unit' => new RentalUnit([
                'capacity' => 2,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'is_active' => true,
                'featured' => false,
            ]),
            'houses' => House::query()->orderBy('name')->get(),
            'amenities' => Amenity::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $amenityIds = $data['amenity_ids'];
        unset($data['amenity_ids']);

        $unit = RentalUnit::query()->create($data);
        $unit->amenities()->sync($amenityIds);

        return redirect()
            ->route('admin.rental-units.edit', $unit->id)
            ->with('status', 'Unidade criada com sucesso.');
    }

    public function edit(RentalUnit $rentalUnit): View
    {
        $rentalUnit->load(['amenities', 'photos']);

        return view('admin.rental-units.create-or-update', [
            'unit' => $rentalUnit,
            'houses' => House::query()->orderBy('name')->get(),
            'amenities' => Amenity::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, RentalUnit $rentalUnit): RedirectResponse
    {
        $data = $this->validatedData($request, $rentalUnit);
        $amenityIds = $data['amenity_ids'];
        unset($data['amenity_ids']);

        $rentalUnit->update($data);
        $rentalUnit->amenities()->sync($amenityIds);

        return redirect()
            ->route('admin.rental-units.edit', $rentalUnit->id)
            ->with('status', 'Unidade atualizada com sucesso.');
    }

    public function destroy(RentalUnit $rentalUnit): RedirectResponse
    {
        if ($rentalUnit->blockedDates()->exists() || $rentalUnit->bookingRequests()->exists()) {
            return back()->with('error', 'Nao e possivel apagar uma unidade com datas bloqueadas ou pedidos de reserva.');
        }

        $rentalUnit->delete();

        return redirect()
            ->route('admin.rental-units.index')
            ->with('status', 'Unidade apagada com sucesso.');
    }

    private function validatedData(Request $request, ?RentalUnit $rentalUnit = null): array
    {
        $slug = $request->filled('slug')
            ? Str::slug($request->input('slug'))
            : Str::slug($request->input('name', ''));

        $request->merge(['slug' => $slug]);

        $houseId = $request->input('house_id');

        $data = $request->validate([
            'house_id' => ['required', 'integer', 'exists:houses,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('rental_units', 'slug')
                    ->where(fn ($query) => $query->where('house_id', $houseId))
                    ->ignore($rentalUnit?->id),
            ],
            'type' => ['required', 'string', 'max:100'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'capacity' => ['required', 'integer', 'min:1'],
            'bedrooms' => ['nullable', 'integer', 'min:0'],
            'bathrooms' => ['nullable', 'integer', 'min:0'],
            'base_price' => ['nullable', 'numeric', 'min:0'],
            'rules' => ['nullable', 'string'],
            'amenity_ids' => ['nullable', 'array'],
            'amenity_ids.*' => ['integer', 'exists:amenities,id'],
            'is_active' => ['nullable', 'boolean'],
            'featured' => ['nullable', 'boolean'],
        ]);

        return [
            'house_id' => $data['house_id'],
            'name' => $data['name'],
            'slug' => $data['slug'],
            'type' => $data['type'],
            'short_description' => $data['short_description'] ?? '',
            'description' => $data['description'] ?? '',
            'capacity' => $data['capacity'],
            'bedrooms' => $data['bedrooms'] ?? 0,
            'bathrooms' => $data['bathrooms'] ?? 0,
            'base_price' => $data['base_price'] ?? null,
            'rules' => $data['rules'] ?? null,
            'amenity_ids' => $data['amenity_ids'] ?? [],
            'is_active' => $request->boolean('is_active'),
            'featured' => $request->boolean('featured'),
        ];
    }
}
