<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public const CATEGORIES = [
        'Trilhos',
        'Cascatas',
        'Miradouros',
        'Praias fluviais',
        'Restaurantes',
        'Termas',
        'Locais históricos',
        'Atividades com crianças',
    ];

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $category = $request->query('category');

        $activities = Activity::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->when($category, fn ($query) => $query->where('category', $category))
            ->orderBy('category')
            ->orderBy('title')
            ->paginate(10)
            ->withQueryString();

        return view('admin.activities.index', [
            'activities' => $activities,
            'categories' => self::CATEGORIES,
            'search' => $search,
            'category' => $category,
        ]);
    }

    public function create(): View
    {
        return view('admin.activities.create-or-update', [
            'activity' => new Activity([
                'is_active' => true,
                'is_featured' => false,
            ]),
            'categories' => self::CATEGORIES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $activity = Activity::query()->create($this->validatedData($request));

        return redirect()
            ->route('admin.activities.edit', $activity)
            ->with('status', 'Atividade criada com sucesso.');
    }

    public function edit(Activity $activity): View
    {
        $activity->load('photos');

        return view('admin.activities.create-or-update', [
            'activity' => $activity,
            'categories' => self::CATEGORIES,
        ]);
    }

    public function update(Request $request, Activity $activity): RedirectResponse
    {
        $activity->update($this->validatedData($request, $activity));

        return redirect()
            ->route('admin.activities.edit', $activity)
            ->with('status', 'Atividade atualizada com sucesso.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        $activity->delete();

        return redirect()
            ->route('admin.activities.index')
            ->with('status', 'Atividade apagada com sucesso.');
    }

    private function validatedData(Request $request, ?Activity $activity = null): array
    {
        $slug = $request->filled('slug')
            ? Str::slug($request->input('slug'))
            : Str::slug($request->input('title', ''));

        $request->merge(['slug' => $slug]);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('activities', 'slug')->ignore($activity?->id),
            ],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'distance' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'string', 'max:500'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        return [
            'title' => $data['title'],
            'slug' => $data['slug'],
            'category' => $data['category'],
            'description' => $data['description'],
            'location' => $data['location'] ?? null,
            'distance' => $data['distance'] ?? null,
            'image' => $data['image'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
