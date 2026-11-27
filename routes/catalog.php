<?php

declare(strict_types=1);

use App\Controllers\ProductController;

return [
    [
        'name' => 'catalog.home',
        'method' => 'GET',
        'path' => '/',
        'handler' => [ProductController::class, 'index'],
        'access' => 'public',
    ],
    [
        'name' => 'catalog.index',
        'method' => 'GET',
        'path' => '/products',
        'handler' => [ProductController::class, 'index'],
        'access' => 'public',
    ],
    [
        'name' => 'catalog.show',
        'method' => 'GET',
        'path' => '/products/{id}',
        'handler' => [ProductController::class, 'show'],
        'access' => 'public',
    ],
];
