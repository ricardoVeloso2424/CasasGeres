<?php

namespace App\Providers;

use App\Models\House;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Active houses listed in the public navigation (layout only).
        View::composer('layouts.app', function (\Illuminate\View\View $view): void {
            $view->with('navigationHouses', House::query()
                ->active()
                ->orderByDesc('featured')
                ->orderBy('name')
                ->get(['id', 'name', 'slug']));
        });
    }
}
