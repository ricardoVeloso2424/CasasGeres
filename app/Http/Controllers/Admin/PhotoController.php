<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\House;
use App\Models\Photo;
use App\Models\RentalUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoController extends Controller
{
    private const TYPES = [
        'houses' => [
            'model' => House::class,
            'directory' => 'houses',
        ],
        'rental-units' => [
            'model' => RentalUnit::class,
            'directory' => 'rental-units',
        ],
        'activities' => [
            'model' => Activity::class,
            'directory' => 'activities',
        ],
    ];

    public function store(Request $request, string $type, int $id): RedirectResponse
    {
        $imageable = $this->findImageable($type, $id);

        $data = $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'alt' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_cover' => ['nullable', 'boolean'],
        ]);

        $files = collect($request->file('images', []))->filter();

        if ($files->isEmpty()) {
            return back()->withErrors(['images' => 'Escolha pelo menos uma imagem valida.']);
        }

        $hasCover = $imageable->photos()->where('is_cover', true)->exists();
        $markFirstAsCover = $request->boolean('is_cover') || ! $hasCover;
        $nextSortOrder = $data['sort_order'] ?? ((int) $imageable->photos()->max('sort_order') + 10);

        if ($request->boolean('is_cover')) {
            $this->clearCover($imageable);
        }

        $files->values()->each(function ($file, int $index) use ($imageable, $type, $data, $markFirstAsCover, $nextSortOrder): void {
            $extension = strtolower($file->extension() ?: $file->getClientOriginalExtension());
            $path = $file->storeAs(self::TYPES[$type]['directory'], Str::uuid().'.'.$extension, 'public');

            $imageable->photos()->create([
                'path' => $path,
                'alt' => $data['alt'] ?? null,
                'sort_order' => $nextSortOrder + ($index * 10),
                'is_cover' => $markFirstAsCover && $index === 0,
            ]);
        });

        return back()->with('status', 'Imagem carregada com sucesso.');
    }

    public function update(Request $request, Photo $photo): RedirectResponse
    {
        $data = $request->validate([
            'alt' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $photo->update([
            'alt' => $data['alt'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return back()->with('status', 'Imagem atualizada com sucesso.');
    }

    public function cover(Photo $photo): RedirectResponse
    {
        $imageable = $photo->imageable;

        if (! $imageable) {
            abort(404);
        }

        $this->clearCover($imageable);
        $photo->update(['is_cover' => true]);

        return back()->with('status', 'Imagem de capa atualizada com sucesso.');
    }

    public function destroy(Photo $photo): RedirectResponse
    {
        $imageable = $photo->imageable;
        $wasCover = $photo->is_cover;
        $path = $photo->path;
        $isStoredLocally = $photo->isStoredLocally();

        $photo->delete();

        if ($isStoredLocally) {
            Storage::disk('public')->delete($path);
        }

        if ($wasCover && $imageable && ! $imageable->photos()->where('is_cover', true)->exists()) {
            $imageable->photos()->orderBy('sort_order')->first()?->update(['is_cover' => true]);
        }

        return back()->with('status', 'Imagem apagada com sucesso.');
    }

    private function findImageable(string $type, int $id): Model
    {
        abort_unless(array_key_exists($type, self::TYPES), 404);

        $modelClass = self::TYPES[$type]['model'];

        return $modelClass::query()->findOrFail($id);
    }

    private function clearCover(Model $imageable): void
    {
        $imageable->photos()->update(['is_cover' => false]);
    }
}
