<?php

return [
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
