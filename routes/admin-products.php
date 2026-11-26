
<?php

declare(strict_types=1);

use App\Controllers\AdminProductController;

return [

    // ==========================================
    // 1. DANH SACH SAN PHAM
    // ==========================================

    [
        'name' => 'admin.products.index',

        'method' => 'GET',

        'path' => '/admin/products',

        'handler' => [
            AdminProductController::class,
            'index'
        ],

        'access' => 'products.manage'
    ],

    // ==========================================
    // 2. HIEN THI FORM THEM SAN PHAM
    // ==========================================

    [
        'name' => 'admin.products.create',

        'method' => 'GET',

        'path' => '/admin/products/create',

        'handler' => [
            AdminProductController::class,
            'create'
        ],

        'access' => 'products.manage'
    ],

    // ==========================================
    // 3. LUU SAN PHAM MOI
    // ==========================================

    [
        'name' => 'admin.products.store',

        'method' => 'POST',

        'path' => '/admin/products/create',

        'handler' => [
            AdminProductController::class,
            'store'
        ],

        'access' => 'products.manage'
    ],

    // ==========================================
    // 4. HIEN THI FORM CHINH SUA
    // ==========================================

    [
        'name' => 'admin.products.edit',

        'method' => 'GET',

        'path' => '/admin/products/{id}/edit',

        'handler' => [
            AdminProductController::class,
            'edit'
        ],

        'access' => 'products.manage'
    ],

    // ==========================================
    // 5. CAP NHAT SAN PHAM
    // ==========================================

    [
        'name' => 'admin.products.update',

        'method' => 'POST',

        'path' => '/admin/products/{id}/edit',

        'handler' => [
            AdminProductController::class,
            'update'
        ],

        'access' => 'products.manage'
    ],

    // ==========================================
    // 6. NGUNG KINH DOANH SAN PHAM
    // ==========================================

    [
        'name' => 'admin.products.disable',

        'method' => 'POST',

        'path' => '/admin/products/{id}/disable',

        'handler' => [
            AdminProductController::class,
            'disable'
        ],

        'access' => 'products.manage'
    ],

    // ==========================================
    // 7. DIEU CHINH TON KHO
    // ==========================================

    [
        'name' => 'admin.products.stock',

        'method' => 'POST',

        'path' => '/admin/products/{id}/stock',

        'handler' => [
            AdminProductController::class,
            'adjustStock'
        ],

        'access' => 'products.manage'
    ]

];
