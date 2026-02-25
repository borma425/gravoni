<?php

return [
    'cashup_cash' => [
        'base_url' => env('CASHUP_BASE_URL', env('CLOUDFLARE_WORKER_BASEURL', 'https://cashup.cash/base')),
        'api_key' => env('CASHUP_API_KEY', ''),
        'app_id' => env('CASHUP_APP_ID', ''),
        'enabled' => env('CASHUP_ENABLED', false),
        // للتحويل ورصيد الحساب (نفس الـ API أو مفتاح منفصل)
        'transfer_base_url' => env('CASHUP_TRANSFER_BASE_URL', env('CASHUP_BASE_URL', env('CLOUDFLARE_WORKER_BASEURL', 'https://cashup.cash/base'))),
        'transfer_api_key' => env('CASHUP_TRANSFER_API_KEY', env('CASHUP_API_KEY', '')),
    ],
    'mylerz' => [
        'username' => env('MYLERZ_USERNAME', ''),
        'password' => env('MYLERZ_PASSWORD', ''),
        'country' => env('MYLERZ_COUNTRY', 'EG'),
        'warehouse' => env('MYLERZ_WAREHOUSE', 'Main Warehouse'),
        'service_type' => env('MYLERZ_SERVICE_TYPE', 'DTD'),
        'address_category' => env('MYLERZ_ADDRESS_CATEGORY', 'H'),
        'default_zone' => env('MYLERZ_DEFAULT_ZONE', 'NASR'),
        'enabled' => env('MYLERZ_ENABLED', false),
    ],
];
