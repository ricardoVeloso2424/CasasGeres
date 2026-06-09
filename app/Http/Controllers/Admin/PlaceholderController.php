<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PlaceholderController extends Controller
{
    public function show(string $section): View
    {
        $sections = [
            'houses' => 'Gerir casas',
            'rental-units' => 'Gerir unidades',
            'activities' => 'Atividades',
            'booking-requests' => 'Pedidos de reserva',
            'contact-messages' => 'Mensagens',
            'calendar-sources' => 'Calendarios iCal',
            'amenities' => 'Comodidades',
        ];

        abort_unless(array_key_exists($section, $sections), 404);

        return view('admin.placeholder', [
            'title' => $sections[$section],
            'section' => $section,
        ]);
    }
}
