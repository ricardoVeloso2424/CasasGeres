<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\RentalUnit;
use Illuminate\View\View;

class HouseController extends Controller
{
    public function index(): View
    {
        $houses = House::query()
            ->active()
            ->with(['photos', 'rentalUnits' => fn ($query) => $query->active()])
            ->withCount(['rentalUnits' => fn ($query) => $query->active()])
            ->latest()
            ->get();

        return view('houses.index', [
            'houses' => $houses,
            'seo' => [
                'title' => 'Casas e unidades para alugar no Geres',
                'description' => 'Veja casas familiares disponiveis no Geres, com T1, T2 e casa inteira para reserva direta.',
                'canonical' => route('houses.index'),
                'image' => config('site.default_og_image'),
            ],
        ]);
    }

    public function show(House $house): View
    {
        abort_unless($house->is_active, 404);

        $house->load([
            'photos',
            'rentalUnits' => fn ($query) => $query
                ->active()
                ->with(['photos', 'amenities', 'blockedDates'])
                ->orderBy('name'),
        ]);

        $cover = $house->coverImageUrl();

        return view('houses.show', [
            'house' => $house,
            'seo' => [
                'title' => "{$house->name} no Geres",
                'description' => "{$house->short_description} Localizacao: {$house->location}.",
                'canonical' => route('houses.show', $house),
                'image' => $cover ?? config('site.default_og_image'),
            ],
        ]);
    }

    public function unit(House $house, RentalUnit $unit): View
    {
        abort_unless($house->is_active && $unit->is_active && $unit->house_id === $house->id, 404);

        $house->load('photos');
        $unit->load([
            'house.photos',
            'photos',
            'amenities',
            'blockedDates' => fn ($query) => $query
                ->where('ends_at', '>=', today()->toDateString())
                ->with('calendarSource')
                ->orderBy('starts_at'),
        ]);

        $cover = $unit->coverImageUrl() ?? $house->coverImageUrl();
        $description = "{$unit->type} para ate {$unit->capacity} hospedes. {$unit->short_description}";
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Accommodation',
            'name' => "{$unit->name} - {$house->name}",
            'description' => $description,
            'url' => route('houses.units.show', [$house, $unit]),
            'telephone' => config('site.phone'),
            'email' => config('site.email'),
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => $house->location ?: config('site.location'),
                'addressCountry' => 'PT',
            ],
        ];

        if ($cover) {
            $schema['image'] = $cover;
        }

        return view('houses.unit', [
            'house' => $house,
            'unit' => $unit,
            'seo' => [
                'title' => "{$unit->name} - {$house->name} no Geres",
                'description' => $description,
                'canonical' => route('houses.units.show', [$house, $unit]),
                'image' => $cover ?? config('site.default_og_image'),
            ],
            'schema' => $schema,
        ]);
    }
}
