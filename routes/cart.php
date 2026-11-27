<?php

declare(strict_types=1);

use App\Controllers\CartController;

return [
    [
        'name' => 'cart.index',
        'method' => 'GET',
        'path' => '/cart',
        'handler' => [CartController::class, 'index'],
        'access' => 'login',
    ],
    [
        'name' => 'cart.add',
        'method' => 'POST',
        'path' => '/cart/add',
        'handler' => [CartController::class, 'add'],
        'access' => 'login',
    ],
    [
        'name' => 'cart.update',
        'method' => 'POST',
        'path' => '/cart/update',
        'handler' => [CartController::class, 'update'],
        'access' => 'login',
    ],
    [
        'name' => 'cart.remove',
        'method' => 'POST',
        'path' => '/cart/remove',
        'handler' => [CartController::class, 'remove'],
        'access' => 'login',
    ],
];