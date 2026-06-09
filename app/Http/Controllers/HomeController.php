<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\House;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $houses = House::query()
            ->active()
            ->featured()
            ->with(['photos', 'rentalUnits' => fn ($query) => $query->active()])
            ->withCount(['rentalUnits' => fn ($query) => $query->active()])
            ->latest()
            ->take(3)
            ->get();

        $activities = Activity::query()
            ->active()
            ->featured()
            ->with('photos')
            ->latest()
            ->take(3)
            ->get();

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'LodgingBusiness',
            'name' => config('site.name'),
            'description' => config('site.default_description'),
            'url' => route('home'),
            'telephone' => config('site.phone'),
            'email' => config('site.email'),
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => config('site.location'),
                'addressCountry' => 'PT',
            ],
        ];

        if (config('site.default_og_image')) {
            $schema['image'] = config('site.default_og_image');
        }

        return view('pages.home', [
            'houses' => $houses,
            'activities' => $activities,
            'seo' => [
                'title' => 'Casas no Gerês para férias em família',
                'description' => config('site.default_description'),
                'canonical' => route('home'),
                'image' => config('site.default_og_image'),
            ],
            'schema' => $schema,
        ]);
    }
}
