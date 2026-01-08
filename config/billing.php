<?php

declare(strict_types=1);

return [
    'dlocalgo' => [
        'base_url' => env('DLOCALGO_BASE_URL', 'https://api-sbx.dlocalgo.com'),
        'api_key' => env('DLOCALGO_API_KEY', ''),
        'secret_key' => env('DLOCALGO_SECRET_KEY', ''),
        'default_currency' => env('DLOCALGO_CURRENCY', 'USD'),
        'default_country' => env('DLOCALGO_COUNTRY', 'CO'),
    ],
];
