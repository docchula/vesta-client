<?php

return [
    'secret' => env('VESTA_SECRET'),
    'issuer' => env('VESTA_ISSUER', env('APP_URL')),
    'url' => env('VESTA_URL', 'https://vesta.mdcu.keendev.net'),
];
