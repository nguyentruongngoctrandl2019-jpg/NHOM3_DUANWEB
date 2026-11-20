<?php

require_once __DIR__ . '/../Support/View.php';
require_once __DIR__ . '/../Services/CartService.php';

use App\Support\View;
use App\Services\CartService;
// =====================================
// 1. DỮ LIỆU 20 SẢN PHẨM MẪU
// =====================================

$products = [
    [
        'id' => 1,
        'name' => 'iPhone 16',
        'price' => 20000000,
        'old_price' => 23000000,
        'image' => '',
        'category' => 'dien-thoai',
        'sold' => 150,
        'featured' => true,
        'description' => 'Điện thoại Apple iPhone 16 chính hãng.'
    ],
    [
        'id' => 2,
        'name' => 'Laptop ASUS Vivobook',
        'price' => 15000000,
        'old_price' => 17000000,
        'image' => '',
        'category' => 'laptop',
        'sold' => 120,
        'featured' => true,
        'description' => 'Laptop ASUS phục vụ học tập và làm việc.'
    ],
    [
        'id' => 3,
        'name' => 'Tai nghe Sony WH-CH520',
        'price' => 1500000,
        'old_price' => 1900000,
        'image' => '',
        'category' => 'tai-nghe',
        'sold' => 180,
        'featured' => true,
        'description' => 'Tai nghe không dây chất lượng cao.'
        'name' => 'Tai nghe Sony',
        'price' => 1500000,
        'image' => '',
        'description' => 'Tai nghe Sony âm thanh chất lượng cao.'
    ],
    [
        'id' => 4,
        'name' => 'Bàn phím Logitech',
        'price' => 850000,
        'old_price' => 990000,
        'image' => '',
        'category' => 'ban-phim',
        'sold' => 90,
        'featured' => false,
        'description' => 'Bàn phím Logitech thiết kế hiện đại.'
    ],
    [
        'id' => 5,
        'name' => 'Samsung Galaxy S24',
        'price' => 18990000,
        'old_price' => 21990000,
        'image' => '',
        'category' => 'dien-thoai',
        'sold' => 130,
        'featured' => true,
        'description' => 'Điện thoại Samsung hiệu năng mạnh mẽ.'
    ],
    [
        'id' => 6,
        'name' => 'Xiaomi Redmi Note 14',
        'price' => 5490000,
        'old_price' => 5990000,
        'image' => '',
        'category' => 'dien-thoai',
        'sold' => 210,
        'featured' => false,
        'description' => 'Điện thoại Xiaomi giá tốt, pin lâu.'
    ],
    [
        'id' => 7,
        'name' => 'OPPO Reno 13',
        'price' => 11990000,
        'old_price' => 12990000,
        'image' => '',
        'category' => 'dien-thoai',
        'sold' => 85,
        'featured' => true,
        'description' => 'Điện thoại OPPO thiết kế trẻ trung.'
    ],
    [
        'id' => 8,
        'name' => 'MacBook Air M3',
        'price' => 24990000,
        'old_price' => 27990000,
        'image' => '',
        'category' => 'laptop',
        'sold' => 110,
        'featured' => true,
        'description' => 'Laptop Apple mỏng nhẹ, hiệu năng cao.'
    ],
    [
        'id' => 9,
        'name' => 'Laptop Dell Inspiron',
        'price' => 16990000,
        'old_price' => 16990000,
        'image' => '',
        'category' => 'laptop',
        'sold' => 65,
        'featured' => false,
        'description' => 'Laptop phù hợp học tập và văn phòng.'
    ],
    [
        'id' => 10,
        'name' => 'Laptop HP Pavilion',
        'price' => 15990000,
        'old_price' => 17990000,
        'image' => '',
        'category' => 'laptop',
        'sold' => 75,
        'featured' => true,
        'description' => 'Laptop HP thiết kế đẹp, dễ sử dụng.'
    ],
    [
        'id' => 11,
        'name' => 'Màn hình LG 24 inch',
        'price' => 2990000,
        'old_price' => 3490000,
        'image' => '',
        'category' => 'man-hinh',
        'sold' => 95,
        'featured' => true,
        'description' => 'Màn hình LG hiển thị sắc nét.'
    ],
    [
        'id' => 12,
        'name' => 'Màn hình Samsung 27 inch',
        'price' => 4590000,
        'old_price' => 4990000,
        'image' => '',
        'category' => 'man-hinh',
        'sold' => 60,
        'featured' => false,
        'description' => 'Màn hình lớn phù hợp làm việc và giải trí.'
    ],
    [
        'id' => 13,
        'name' => 'Màn hình MSI Gaming',
        'price' => 5990000,
        'old_price' => 5990000,
        'image' => '',
        'category' => 'man-hinh',
        'sold' => 45,
        'featured' => true,
        'description' => 'Màn hình dành cho người yêu thích gaming.'
    ],
    [
        'id' => 14,
        'name' => 'Apple AirPods 4',
        'price' => 3490000,
        'old_price' => 3990000,
        'image' => '',
        'category' => 'tai-nghe',
        'sold' => 160,
        'featured' => true,
        'description' => 'Tai nghe không dây Apple.'
    ],
    [
        'id' => 15,
        'name' => 'Tai nghe JBL Tune 520BT',
        'price' => 1290000,
        'old_price' => 1590000,
        'image' => '',
        'category' => 'tai-nghe',
        'sold' => 105,
        'featured' => false,
        'description' => 'Tai nghe Bluetooth âm thanh sống động.'
    ],
    [
        'id' => 16,
        'name' => 'Bàn phím cơ AKKO',
        'price' => 1490000,
        'old_price' => 1690000,
        'image' => '',
        'category' => 'ban-phim',
        'sold' => 80,
        'featured' => true,
        'description' => 'Bàn phím cơ thiết kế đẹp mắt.'
    ],
    [
        'id' => 17,
        'name' => 'Bàn phím Razer BlackWidow',
        'price' => 2490000,
        'old_price' => 2490000,
        'image' => '',
        'category' => 'ban-phim',
        'sold' => 55,
        'featured' => false,
        'description' => 'Bàn phím cơ dành cho game thủ.'
    ],
    [
        'id' => 18,
        'name' => 'Chuột Logitech G102',
        'price' => 399000,
        'old_price' => 590000,
        'image' => '',
        'category' => 'phu-kien',
        'sold' => 250,
        'featured' => true,
        'description' => 'Chuột gaming Logitech độ nhạy cao.'
    ],
    [
        'id' => 19,
        'name' => 'Sạc dự phòng Anker 10000mAh',
        'price' => 690000,
        'old_price' => 890000,
        'image' => '',
        'category' => 'phu-kien',
        'sold' => 140,
        'featured' => false,
        'description' => 'Sạc dự phòng nhỏ gọn, tiện lợi.'
    ],
    [
        'id' => 20,
        'name' => 'Loa Bluetooth JBL Flip 6',
        'price' => 2490000,
        'old_price' => 2990000,
        'image' => '',
        'category' => 'phu-kien',
        'sold' => 115,
        'featured' => true,
        'description' => 'Loa Bluetooth âm thanh mạnh mẽ.'
    ]
];

// =====================================
// 2. DANH MỤC SẢN PHẨM
// =====================================

$categories = [
    ['name' => 'Điện thoại', 'slug' => 'dien-thoai'],
    ['name' => 'Laptop', 'slug' => 'laptop'],
    ['name' => 'Màn hình', 'slug' => 'man-hinh'],
    ['name' => 'Tai nghe', 'slug' => 'tai-nghe'],
    ['name' => 'Bàn phím', 'slug' => 'ban-phim'],
    ['name' => 'Phụ kiện', 'slug' => 'phu-kien']
];

// =====================================
// 3. PHÂN LOẠI SẢN PHẨM TRANG CHỦ
// =====================================

// Sản phẩm giảm giá
$saleProducts = array_values(
    array_filter($products, function ($product) {
        return $product['old_price'] > $product['price'];
    })
);

$saleProducts = array_slice($saleProducts, 0, 8);

// Sản phẩm bán chạy
$bestSellers = $products;

usort($bestSellers, function ($a, $b) {
    return $b['sold'] <=> $a['sold'];
});

$bestSellers = array_slice($bestSellers, 0, 8);

// Sản phẩm nổi bật
$featuredProducts = array_values(
    array_filter($products, function ($product) {
        return $product['featured'] === true;
    })
);

$featuredProducts = array_slice($featuredProducts, 0, 8);

// =====================================
// 4. KHỞI TẠO GIỎ HÀNG
// =====================================

$cart = new CartService();

$path = parse_url(
    $_SERVER['REQUEST_URI'],
    PHP_URL_PATH
) ?: '/';

$method = $_SERVER['REQUEST_METHOD'];

// =====================================
// 5. HIỂN THỊ TRANG CHỦ
// =====================================

if ($path === '/' && $method === 'GET') {

    View::render('home/index', [
        'pageTitle' => 'Trang chủ - CT TECH STORE',
        'products' => $products,
        'categories' => $categories,
        'saleProducts' => $saleProducts,
        'bestSellers' => $bestSellers,
        'featuredProducts' => $featuredProducts
    ]);

    exit;
}

// =====================================
// 6. DANH SÁCH VÀ TÌM KIẾM SẢN PHẨM
// =====================================

if ($path === '/products' && $method === 'GET') {

    $search = trim((string) ($_GET['search'] ?? ''));
    $category = (string) ($_GET['category'] ?? '');

    $filteredProducts = $products;

    // Lọc theo danh mục
    if ($category !== '') {

        $filteredProducts = array_filter(
            $filteredProducts,
            function ($product) use ($category) {
                return $product['category'] === $category;
            }
        );
    }

    // Tìm kiếm theo tên sản phẩm
    if ($search !== '') {

        $filteredProducts = array_filter(
            $filteredProducts,
            function ($product) use ($search) {
                return stripos(
                    $product['name'],
                    $search
                ) !== false;
            }
        );
    }

    View::render('catalog/index', [
        'pageTitle' => 'Sản phẩm - CT TECH STORE',
        'products' => array_values($filteredProducts),
        'categories' => $categories,
        'search' => $search,
        'category' => $category
    ]);

    exit;
}

// =====================================
// 7. CHI TIẾT SẢN PHẨM
// =====================================


// =====================================
// CHI TIẾT SẢN PHẨM
// =====================================

if (
    $method === 'GET' &&
    preg_match('#^/products/([0-9]+)$#', $path, $matches)
) {
    $productId = (int) $matches[1];
    $selectedProduct = null;

    // Tìm sản phẩm đang xem
    foreach ($products as $product) {
        if ($product['id'] === $productId) {
            $selectedProduct = $product;
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

    if ($selectedProduct === null) {
        http_response_code(404);
        exit('Không tìm thấy sản phẩm.');
    }

    // Sản phẩm liên quan
    $relatedProducts = [];

    // Sản phẩm đề xuất
    $recommendedProducts = [];

    foreach ($products as $item) {

        // Bỏ qua sản phẩm đang xem
        if ($item['id'] === $productId) {
            continue;
        }

        if ($item['category'] === $selectedProduct['category']) {
            $relatedProducts[] = $item;
        } else {
            $recommendedProducts[] = $item;
        }
    }

    $relatedProducts = array_slice($relatedProducts, 0, 4);

    $recommendedProducts = array_slice(
        $recommendedProducts,
        0,
        8
    );

    View::render('catalog/show', [
        'pageTitle' => $selectedProduct['name'],
        'product' => $selectedProduct,
        'relatedProducts' => $relatedProducts,
        'recommendedProducts' => $recommendedProducts
    ]);

    exit;
}


// =====================================
// 8. THÊM SẢN PHẨM VÀO GIỎ
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

    if (
        !$productId ||
        $productId < 1 ||
        !$quantity ||
        $quantity < 1 ||
        $quantity > 10
    ) {
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
// 9. CẬP NHẬT SỐ LƯỢNG
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

    if (
        !$productId ||
        $productId < 1 ||
        !$quantity ||
        $quantity < 1 ||
        $quantity > 10
    ) {
        http_response_code(400);
        exit('Số lượng không hợp lệ.');
    }

    $cart->update($productId, $quantity);

    header('Location: /cart', true, 303);
    exit;
}

// =====================================
// 10. XÓA SẢN PHẨM
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
// 11. HIỂN THỊ GIỎ HÀNG
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
// 12. ĐƯỜNG DẪN KHÔNG TỒN TẠI
// =====================================

http_response_code(404);
echo '404 - Không tìm thấy trang.';
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
