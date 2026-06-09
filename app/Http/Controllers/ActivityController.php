<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(): View
    {
        $activities = Activity::query()
            ->active()
            ->with('photos')
            ->orderByDesc('is_featured')
            ->orderBy('category')
            ->orderBy('title')
            ->get()
            ->groupBy('category');

        return view('activities.index', [
            'activitiesByCategory' => $activities,
            'seo' => [
                'title' => 'Atividades, trilhos e locais a visitar no Geres',
                'description' => 'Sugestoes de trilhos, cascatas, miradouros, praias fluviais, termas e locais historicos perto dos alojamentos no Geres.',
                'canonical' => route('activities.index'),
                'image' => config('site.default_og_image'),
            ],
        ]);
    }
}
