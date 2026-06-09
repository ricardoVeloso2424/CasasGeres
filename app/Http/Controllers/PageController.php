<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('pages.about', [
            'seo' => [
                'title' => 'Sobre nos - alojamento familiar no Geres',
                'description' => 'Conheca o projeto familiar por tras das casas de alojamento local no Geres e a forma simples de reserva direta.',
                'canonical' => route('pages.about'),
                'image' => config('site.default_og_image'),
            ],
        ]);
    }

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
