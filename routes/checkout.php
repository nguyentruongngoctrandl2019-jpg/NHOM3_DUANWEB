<?php

declare(strict_types=1);

use App\Controllers\CheckoutController;

return [
    [
        'name' => 'checkout',
        'method' => 'GET',
        'path' => '/checkout',
        'handler' => [
            CheckoutController::class,
            'index'
        ],
        'access' => 'login',
    ],
    [
        'name' => 'checkout.place',
        'method' => 'POST',
        'path' => '/checkout',
        'handler' => [
            CheckoutController::class,
            'place'
        ],
        'access' => 'login',
    ],
];