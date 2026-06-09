<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AmenityController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $amenities = Amenity::query()
            ->withCount('rentalUnits')
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.amenities.index', [
            'amenities' => $amenities,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.amenities.create-or-update', [
            'amenity' => new Amenity(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $amenity = Amenity::query()->create($this->validatedData($request));

        return redirect()
            ->route('admin.amenities.edit', $amenity)
            ->with('status', 'Comodidade criada com sucesso.');
    }

    public function edit(Amenity $amenity): View
    {
        return view('admin.amenities.create-or-update', [
            'amenity' => $amenity,
        ]);
    }

    public function update(Request $request, Amenity $amenity): RedirectResponse
    {
        $amenity->update($this->validatedData($request, $amenity));

        return redirect()
            ->route('admin.amenities.edit', $amenity)
            ->with('status', 'Comodidade atualizada com sucesso.');
    }

    public function destroy(Amenity $amenity): RedirectResponse
    {
        if ($amenity->rentalUnits()->exists()) {
            return back()->with('error', 'Nao e possivel apagar uma comodidade associada a unidades.');
        }

        $amenity->delete();

        return redirect()
            ->route('admin.amenities.index')
            ->with('status', 'Comodidade apagada com sucesso.');
    }

    private function validatedData(Request $request, ?Amenity $amenity = null): array
    {
        $slug = $request->filled('slug')
            ? Str::slug($request->input('slug'))
            : Str::slug($request->input('name', ''));

        $request->merge(['slug' => $slug]);

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('amenities', 'slug')->ignore($amenity?->id),
            ],
            'icon' => ['nullable', 'string', 'max:100'],
        ]);
    }
}
