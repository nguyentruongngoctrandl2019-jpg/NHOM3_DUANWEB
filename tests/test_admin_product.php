<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Support\Database;
use App\Models\AdminProduct;
use App\Models\Product;

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }

    echo 'PASS: ' . $message . PHP_EOL;
}

try {

    $pdo = Database::connect();

    echo "PASS: Ket noi PDO" . PHP_EOL;

    $model = new AdminProduct($pdo);

    $catalog = new Product($pdo);

    // TEST 1: Danh sach san pham

    $products = $model->all();

    check(
        is_array($products),
        'Lay danh sach san pham'
    );

    echo 'Tong san pham: '
        . count($products) . PHP_EOL;

    // TEST 2: Danh muc

    $categories = $model->categories();

    check(
        !empty($categories),
        'Lay danh sach danh muc'
    );

    $categoryId = (int) $categories[0]['id'];

    // Tao du lieu kiem thu rieng

    $data = [
        'category_id' => $categoryId,
        'sku' => 'TEST-' . bin2hex(random_bytes(6)),
        'name' => 'San pham test B04',
        'description' => 'Du lieu kiem thu',
        'price' => '5000000.00',
        'warranty_months' => 12,
        'is_active' => 1,
        'stock' => 10
    ];

    // TEST 3: Them san pham

    $id = $model->create($data);

    check(
        $id > 0,
        'Them san pham'
    );

    echo 'ID san pham test: '
        . $id . PHP_EOL;

    // TEST 4: Kiem tra du lieu

    $product = $model->find($id);

    check(
        $product !== null,
        'Tim san pham theo ID'
    );

    check(
        (int) $product['stock'] === 10,
        'Ton kho ban dau'
    );

    // TEST 5: SKU trung

    $duplicateRejected = false;

    try {

        $model->create($data);

    } catch (InvalidArgumentException $e) {

        $duplicateRejected = true;
    }

    check(
        $duplicateRejected,
        'Tu choi SKU trung lap'
    );

    // TEST 6: Cap nhat san pham

    $data['name'] = 'San pham B04 da cap nhat';

    $data['price'] = '6000000.00';

    $model->update($id, $data);

    $product = $model->find($id);

    check(
        $product['name'] === $data['name'],
        'Cap nhat ten san pham'
    );

    check(
        (string) $product['price'] === '6000000.00',
        'Cap nhat gia san pham'
    );

    // TEST 7: Tang ton kho

    $model->adjustStock($id, 5);

    $product = $model->find($id);

    check(
        (int) $product['stock'] === 15,
        'Cap nhat ton kho'
    );

    // TEST 8: Khong cho ton kho am

    $negativeRejected = false;

    try {

        $model->adjustStock($id, -100);

    } catch (InvalidArgumentException $e) {

        $negativeRejected = true;
    }

    check(
        $negativeRejected,
        'Khong cho ton kho am'
    );

    $product = $model->find($id);

    check(
        (int) $product['stock'] === 15,
        'Ton kho khong bi thay doi khi loi'
    );

    // TEST 9: Kiem tra lich su kho

    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM stock_movements
        WHERE product_id = :id
    ");

    $stmt->execute(['id' => $id]);

    check(
        (int) $stmt->fetchColumn() === 2,
        'Ghi lich su thay doi ton kho'
    );

    // TEST 10: San pham dang ban

    check(
        $catalog->findActive($id) !== null,
        'San pham xuat hien trong catalog'
    );

    // TEST 11: Ngung kinh doanh

    check(
        $model->disable($id),
        'Ngung kinh doanh san pham'
    );

    $product = $model->find($id);

    check(
        (int) $product['is_active'] === 0,
        'Trang thai ngung kinh doanh'
    );

    // TEST 12: Kiem tra catalog

    check(
        $catalog->findActive($id) === null,
        'San pham ngung ban bien mat khoi catalog'
    );

    echo PHP_EOL;

    echo "PASS: All AdminProduct tests"
        . PHP_EOL;

} catch (Throwable $e) {

    echo 'FAIL: '
        . $e->getMessage()
        . PHP_EOL;

    exit(1);
}