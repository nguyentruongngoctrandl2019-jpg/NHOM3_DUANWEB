<?php

declare(strict_types=1);

use App\Controllers\AdminProductController;

return [
    [
        'name' => 'admin.products.index',
        'method' => 'GET',
        'path' => '/admin/products',
        'handler' => [AdminProductController::class, 'index'],
        'access' => 'products.manage',
    ],
    [
        'name' => 'admin.products.create',
        'method' => 'GET',
        'path' => '/admin/products/create',
        'handler' => [AdminProductController::class, 'create'],
        'access' => 'products.manage',
    ],
    [
        'name' => 'admin.products.store',
        'method' => 'POST',
        'path' => '/admin/products/create',
        'handler' => [AdminProductController::class, 'create'],
        'access' => 'products.manage',
    ],
    [
        'name' => 'admin.products.edit',
        'method' => 'GET',
        'path' => '/admin/products/{id}/edit',
        'handler' => [AdminProductController::class, 'edit'],
        'access' => 'products.manage',
    ],
    [
        'name' => 'admin.products.update',
        'method' => 'POST',
        'path' => '/admin/products/{id}/edit',
        'handler' => [AdminProductController::class, 'edit'],
        'access' => 'products.manage',
    ],
    [
        'name' => 'admin.products.disable',
        'method' => 'POST',
        'path' => '/admin/products/{id}/disable',
        'handler' => [AdminProductController::class, 'disable'],
        'access' => 'products.manage',
    ],
];