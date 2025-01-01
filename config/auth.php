<?php


use App\Models\Client;
use App\Models\User;

return [
    'defaults' => [
        'guard'     => 'api',
        'passwords' => 'users',
    ],
    'guards' => [
        'api' => [
            'driver'   => 'sanctum',
            'provider' => 'users',
        ],
        'api-clients' => [
            'driver'   => 'sanctum',
            'provider' => 'clients',
        ],
    ],
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model'  => User::class,
        ],

        'clients' => [
            'driver' => 'eloquent',
            'model'  => Client::class,
        ],
    ],
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],
    'password_timeout' => 10800,
];
