<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function faq(): View
    {
        return view('pages.faq', [
            'seo' => [
                'title' => 'Perguntas frequentes sobre reservas no Geres',
                'description' => 'Respostas rapidas sobre check-in, check-out, Wi-Fi, estacionamento, animais, disponibilidade e reservas diretas.',
                'canonical' => route('pages.faq'),
                'image' => config('site.default_og_image'),
            ],
        ]);
    }
}
