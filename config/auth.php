<?php

declare(strict_types=1);

return [
    'default' => [
        'guard' => 'session',
        'provider' => 'users',
    ],
    'guards' => [
        'session' => [
            'driver' => Stru\StruHyperfAuth\Guard\SessionGuard::class,
            'provider' => 'users',
        ],
        'api' => [
            'driver' => Stru\StruHyperfAuth\Guard\JwtGuard::class,
            'provider' => 'users',
        ],
    ],
    'providers' => [
        'users' => [
            'driver' => Stru\StruHyperfAuth\Provider\EloquentUserProvider::class,
            'model' => App\Model\User::class,
        ],
    ],
];
