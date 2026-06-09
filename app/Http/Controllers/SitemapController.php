<?php

namespace App\Http\Controllers;

use App\Models\House;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            ['loc' => route('home')],
            ['loc' => route('houses.index')],
        ]);

        House::query()
            ->active()
            ->with(['rentalUnits' => fn ($query) => $query->active()])
            ->orderBy('slug')
            ->get()
            ->each(function (House $house) use ($urls): void {
                $urls->push([
                    'loc' => route('houses.show', $house),
                    'lastmod' => $house->updated_at?->toAtomString(),
                ]);

                $house->rentalUnits->each(function ($unit) use ($house, $urls): void {
                    $urls->push([
                        'loc' => route('houses.units.show', [$house, $unit]),
                        'lastmod' => $unit->updated_at?->toAtomString(),
                    ]);
                });
            });

        $urls = $urls->merge([
            ['loc' => route('activities.index')],
            ['loc' => route('contact.index')],
            ['loc' => route('pages.about')],
            ['loc' => route('pages.faq')],
        ]);

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
