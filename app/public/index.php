<?php
declare(strict_types=1);

require_once __DIR__ . '/../Support/View.php';
require_once __DIR__ . '/../Services/CartService.php';

use App\Support\View;
use App\Services\CartService;

// =====================================
// DỮ LIỆU MẪU ĐỂ KIỂM TRA C02 VÀ C03
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

$categories = [
    ['name' => 'Điện thoại', 'slug' => 'dien-thoai'],
    ['name' => 'Laptop', 'slug' => 'laptop'],
    ['name' => 'Phụ kiện', 'slug' => 'phu-kien']
];

// =====================================
// KHỞI TẠO GIỎ HÀNG
// =====================================

$cart = new CartService();

$path = parse_url(
    $_SERVER['REQUEST_URI'],
    PHP_URL_PATH
) ?: '/';

$method = $_SERVER['REQUEST_METHOD'];

// =====================================
// THÊM SẢN PHẨM VÀO GIỎ
// =====================================

if ($path === '/cart/add' && $method === 'POST') {

    $productId = filter_input(
        INPUT_POST,
        'product_id',
        FILTER_VALIDATE_INT
    );

    $quantity = filter_input(
        INPUT_POST,
        'quantity',
        FILTER_VALIDATE_INT
    );

    if (!$productId || !$quantity || $quantity < 1 || $quantity > 10) {
        http_response_code(400);
        exit('Thông tin sản phẩm không hợp lệ.');
    }

    foreach ($products as $product) {
        if ($product['id'] === $productId) {
            $cart->add($product, $quantity);

            header('Location: /cart', true, 303);
            exit;
        }
    }

    http_response_code(404);
    exit('Không tìm thấy sản phẩm.');
}

// =====================================
// CẬP NHẬT SỐ LƯỢNG
// =====================================

if ($path === '/cart/update' && $method === 'POST') {

    $productId = filter_input(
        INPUT_POST,
        'product_id',
        FILTER_VALIDATE_INT
    );

    $quantity = filter_input(
        INPUT_POST,
        'quantity',
        FILTER_VALIDATE_INT
    );

    if (!$productId || !$quantity || $quantity < 1 || $quantity > 10) {
        http_response_code(400);
        exit('Số lượng không hợp lệ.');
    }

    $cart->update($productId, $quantity);

    header('Location: /cart', true, 303);
    exit;
}

// =====================================
// XÓA SẢN PHẨM
// =====================================

if ($path === '/cart/remove' && $method === 'POST') {

    $productId = filter_input(
        INPUT_POST,
        'product_id',
        FILTER_VALIDATE_INT
    );

    if (!$productId || $productId < 1) {
        http_response_code(400);
        exit('Sản phẩm không hợp lệ.');
    }

    $cart->remove($productId);

    header('Location: /cart', true, 303);
    exit;
}

// =====================================
// HIỂN THỊ TRANG GIỎ HÀNG
// =====================================

if ($path === '/cart' && $method === 'GET') {

    View::render('cart/index', [
        'pageTitle' => 'Giỏ hàng - CT TECH STORE',
        'items' => $cart->getItems(),
        'total' => $cart->getTotal()
    ]);

    exit;
}

// =====================================
// HIỂN THỊ CHI TIẾT SẢN PHẨM
// =====================================

if (
    $method === 'GET' &&
    preg_match('#^/products/([0-9]+)$#', $path, $matches)
) {

    $productId = (int) $matches[1];
    $selectedProduct = null;

    foreach ($products as $product) {
        if ($product['id'] === $productId) {
            $selectedProduct = $product;
            break;
        }
    }

    View::render('catalog/show', [
        'pageTitle' => 'Chi tiết sản phẩm',
        'product' => $selectedProduct
    ]);

    exit;
}

// =====================================
// HIỂN THỊ DANH SÁCH SẢN PHẨM
// =====================================

if (
    ($path === '/' || $path === '/products')
    && $method === 'GET'
) {

    View::render('catalog/index', [
        'pageTitle' => 'Sản phẩm - CT TECH STORE',
        'products' => $products,
        'categories' => $categories,
        'search' => ''
    ]);

    exit;
}

// Đường dẫn không tồn tại
http_response_code(404);
echo '404 - Không tìm thấy trang.';
