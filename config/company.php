<?php

return [

    // TODO: replace these placeholder defaults with Aquavia Pools' real contact details in .env
    'whatsapp_number' => env('COMPANY_WHATSAPP_NUMBER', '201000000000'),
    'phone' => env('COMPANY_PHONE', '+20 100 000 0000'),
    'email' => env('COMPANY_EMAIL', 'info@aquaviapools.com'),
    'address' => env('COMPANY_ADDRESS', 'Cairo, Egypt'),
    'map_url' => env('COMPANY_MAP_URL', 'https://maps.google.com'),

    'social' => [
        'facebook' => env('COMPANY_FACEBOOK_URL'),
        'instagram' => env('COMPANY_INSTAGRAM_URL'),
        'tiktok' => env('COMPANY_TIKTOK_URL'),
    ],

];
