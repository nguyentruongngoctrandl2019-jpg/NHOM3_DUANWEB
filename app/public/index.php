<?php
declare(strict_types=1);

require_once __DIR__ . '/../Support/View.php';

use App\Support\View;
// =====================================
// DỮ LIỆU MẪU - KIỂM TRA GIAO DIỆN C02
// =====================================

$products = [
    [
        'id' => 1,
        'name' => 'iPhone 16',
        'price' => 20000000,
        'image' => '',
        'description' => 'Điện thoại Apple iPhone 16 chính hãng.'
    ],
    [
        'id' => 2,
        'name' => 'Laptop ASUS Vivobook',
        'price' => 15000000,
        'image' => '',
        'description' => 'Laptop ASUS phục vụ học tập và làm việc.'
    ],
    [
        'id' => 3,
        'name' => 'Tai nghe Sony',
        'price' => 1500000,
        'image' => '',
        'description' => 'Tai nghe Sony âm thanh chất lượng cao.'
    ],
    [
        'id' => 4,
        'name' => 'Bàn phím Logitech',
        'price' => 850000,
        'image' => '',
        'description' => 'Bàn phím Logitech thiết kế hiện đại.'
    ]
];

// Danh mục sản phẩm
$categories = [
    [
        'name' => 'Điện thoại',
        'slug' => 'dien-thoai'
    ],
    [
        'name' => 'Laptop',
        'slug' => 'laptop'
    ],
    [
        'name' => 'Phụ kiện',
        'slug' => 'phu-kien'
    ]
];

// =====================================
// HIỂN THỊ GIAO DIỆN C02
// =====================================

// Kiểm tra người dùng đang mở trang nào
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Nếu mở chi tiết sản phẩm
if (preg_match('#^/products/([0-9]+)$#', $path, $matches)) {
    $productId = (int) $matches[1];

    $selectedProduct = null;

    foreach ($products as $item) {
        if ($item['id'] === $productId) {
            $selectedProduct = $item;
            break;
        }
    }

    View::render('catalog/show', [
        'pageTitle' => 'Chi tiết sản phẩm',
        'product' => $selectedProduct
    ]);

} else {
    // Hiển thị danh sách sản phẩm
    View::render('catalog/index', [
        'pageTitle' => 'Sản phẩm - CT TECH STORE',
        'products' => $products,
        'categories' => $categories,
        'search' => ''
    ]);
}
