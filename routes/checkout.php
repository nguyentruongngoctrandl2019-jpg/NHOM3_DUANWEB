<?php

declare(strict_types=1);

return [];
return [
    [
        'name' => 'checkout',
        'method' => 'GET',
        'path' => '/checkout',
        'handler' => [App\Controllers\CheckoutController::class, 'index'],
        'access' => 'login',
    ],
    [
        'name' => 'checkout.place',
        'method' => 'POST',
        'path' => '/checkout',
        'handler' => [App\Controllers\CheckoutController::class, 'place'],
        'access' => 'login',
    ],
];
