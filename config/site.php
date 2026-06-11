<?php

$sitePhone = env('SITE_PHONE', '');

return [
    'name' => env('SITE_NAME', env('APP_NAME', 'Casas Geres')),
    'responsible_name' => env('SITE_RESPONSIBLE_NAME', ''),
    'phone' => $sitePhone,
    'phone_href' => preg_replace('/\s+/', '', $sitePhone),
    'whatsapp' => env('SITE_WHATSAPP', ''),
    'email' => env('SITE_EMAIL', ''),
    'location' => env('SITE_LOCATION', 'Gerês, Portugal'),
    'default_description' => env('SITE_DEFAULT_DESCRIPTION', 'Alojamento local familiar no Gerês com casas e unidades independentes para reserva direta.'),
    'default_og_image' => env('SITE_DEFAULT_OG_IMAGE', 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1200&q=80'),
];
