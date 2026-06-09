<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\House;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HouseController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $houses = House::query()
            ->withCount('rentalUnits')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.houses.index', [
            'houses' => $houses,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.houses.create-or-update', [
            'house' => new House([
                'is_active' => true,
                'featured' => false,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $house = House::query()->create($data);

        return redirect()
            ->route('admin.houses.edit', $house)
            ->with('status', 'Casa criada com sucesso.');
    }

    public function edit(House $house): View
    {
        $house->load('photos');

        return view('admin.houses.create-or-update', [
            'house' => $house,
        ]);
    }

    public function update(Request $request, House $house): RedirectResponse
    {
        $data = $this->validatedData($request, $house);

        $house->update($data);

        return redirect()
            ->route('admin.houses.edit', $house)
            ->with('status', 'Casa atualizada com sucesso.');
    }

    public function destroy(House $house): RedirectResponse
    {
        if ($house->rentalUnits()->exists()) {
            return back()->with('error', 'Nao e possivel apagar uma casa com unidades associadas.');
        }

        $house->delete();

        return redirect()
            ->route('admin.houses.index')
            ->with('status', 'Casa apagada com sucesso.');
    }

    private function validatedData(Request $request, ?House $house = null): array
    {
        $slug = $request->filled('slug')
            ? Str::slug($request->input('slug'))
            : Str::slug($request->input('name', ''));

        $request->merge(['slug' => $slug]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('houses', 'slug')->ignore($house?->id),
            ],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'address_optional' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'is_active' => ['nullable', 'boolean'],
            'featured' => ['nullable', 'boolean'],
        ]);

        return [
            'name' => $data['name'],
            'slug' => $data['slug'],
            'short_description' => $data['short_description'] ?? '',
            'description' => $data['description'] ?? '',
            'location' => $data['location'] ?? '',
            'address_optional' => $data['address_optional'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'featured' => $request->boolean('featured'),
        ];
    }
}
