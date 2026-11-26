<?php

declare(strict_types=1);

return [];
return [
    [
        'name' => 'orders.index',
        'method' => 'GET',
        'path' => '/orders',
        'handler' => [App\Controllers\OrderController::class, 'index'],
        'access' => 'login',
    ],
    [
        'name' => 'orders.show',
        'method' => 'GET',
        'path' => '/orders/{publicId}',
        'handler' => [App\Controllers\OrderController::class, 'show'],
        'access' => 'login',
    ],
    [
        'name' => 'admin.orders',
        'method' => 'GET',
        'path' => '/admin/orders',
        'handler' => [App\Controllers\AdminOrderController::class, 'index'],
        'access' => 'orders.manage',
    ],
    [
        'name' => 'admin.orders.status',
        'method' => 'POST',
        'path' => '/admin/orders/{publicId}/status',
        'handler' => [App\Controllers\AdminOrderController::class, 'status'],
        'access' => 'orders.manage',
    ],
];
