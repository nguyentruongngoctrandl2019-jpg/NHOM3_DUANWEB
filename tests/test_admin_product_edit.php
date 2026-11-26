
<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Support\Database;
use App\Models\AdminProduct;
use App\Controllers\AdminProductController;
use GuzzleHttp\Psr7\ServerRequest;

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }

    echo 'PASS: ' . $message . PHP_EOL;
}

try {

    // TEST 1: Ket noi PDO

    $pdo = Database::connect();

    check(
        $pdo instanceof PDO,
        'Ket noi PDO'
    );

    // TEST 2: Khoi tao Model va Controller

    $model = new AdminProduct($pdo);

    $controller = new AdminProductController($model);

    check(
        $controller instanceof AdminProductController,
        'Khoi tao Controller'
    );

    // TEST 3: Kiem tra file View

    $file = __DIR__
        . '/../app/Views/admin/products/edit.php';

    check(
        is_file($file),
        'File edit.php ton tai'
    );

    // TEST 4: Lay san pham tu database

    $products = $model->all();

    check(
        !empty($products),
        'Database co san pham'
    );

    $product = $products[0];

    $id = (int) $product['id'];

    $product = $model->find($id);

    check(
        $product !== null,
        'Tim san pham theo ID'
    );

    // Tao request quan tri gia lap

    $admin = [
        'id' => 1,
        'permissions' => ['products.manage']
    ];

    $request = new ServerRequest(
        'GET',
        '/admin/products/' . $id . '/edit'
    );

    $request = $request
        ->withAttribute('user', $admin)
        ->withAttribute('csrf_token', 'test-token');

    // TEST 5: Hien thi trang edit

    $response = $controller->edit(
        $request,
        ['id' => $id]
    );

    check(
        $response->getStatusCode() === 200,
        'Trang chinh sua HTTP 200'
    );

    $html = (string) $response->getBody();

    // TEST 6: Kiem tra tieu de

    check(
        str_contains($html, 'Chỉnh sửa sản phẩm'),
        'Hien thi tieu de'
    );

    // TEST 7: Kiem tra form update

    check(
        str_contains(
            $html,
            'action="/admin/products/' . $id . '/edit"'
        ),
        'Form cap nhat san pham'
    );

    // TEST 8: Kiem tra du lieu san pham

    $name = htmlspecialchars(
        (string) $product['name'],
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );

    check(
        str_contains($html, 'value="' . $name . '"'),
        'Hien thi ten san pham tu MySQL'
    );

    $sku = htmlspecialchars(
        (string) $product['sku'],
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );

    check(
        str_contains($html, 'value="' . $sku . '"'),
        'Hien thi SKU tu MySQL'
    );

    // TEST 9: Kiem tra cac truong

    $fields = [
        'sku',
        'name',
        'category_id',
        'price',
        'warranty_months',
        'description',
        'is_active',
        'quantity_change',
        '_token'
    ];

    foreach ($fields as $field) {

        check(
            str_contains(
                $html,
                'name="' . $field . '"'
            ),
            'Hien thi truong ' . $field
        );
    }

    // TEST 10: Kiem tra CSRF

    check(
        str_contains($html, 'value="test-token"'),
        'Hien thi CSRF token'
    );

    // TEST 11: Kiem tra form ton kho

    check(
        str_contains(
            $html,
            'action="/admin/products/' . $id . '/stock"'
        ),
        'Form dieu chinh ton kho'
    );

    check(
        str_contains($html, 'Điều chỉnh tồn kho'),
        'Hien thi khu vuc ton kho'
    );

    // TEST 12: Khong gui ton kho trong form edit

    check(
        !str_contains($html, 'name="stock"'),
        'Khong ghi de ton kho khi sua san pham'
    );

    // TEST 13: Kiem tra form ngung kinh doanh

    if ((int) $product['is_active'] === 1) {

        check(
            str_contains(
                $html,
                'action="/admin/products/'
                . $id
                . '/disable"'
            ),
            'Form ngung kinh doanh'
        );

    } else {

        echo 'SKIP: San pham da ngung kinh doanh'
            . PHP_EOL;
    }

    // TEST 14: Du lieu cap nhat khong hop le

    $postRequest = new ServerRequest(
        'POST',
        '/admin/products/' . $id . '/edit'
    );

    $postRequest = $postRequest
        ->withAttribute('user', $admin)
        ->withAttribute('csrf_token', 'test-token')
        ->withParsedBody([
            '_token' => 'test-token',
            'sku' => '',
            'name' => 'Test cap nhat B04',
            'category_id' => $product['category_id'],
            'price' => '-100',
            'warranty_months' => 12,
            'description' => 'Test validation',
            'is_active' => 1
        ]);

    $response = $controller->update(
        $postRequest,
        ['id' => $id]
    );

    check(
        $response->getStatusCode() === 422,
        'Tu choi du lieu cap nhat khong hop le'
    );

    $errorHtml = (string) $response->getBody();

    check(
        str_contains(
            $errorHtml,
            'Không thể cập nhật sản phẩm!'
        ),
        'Hien thi thong bao loi'
    );

    check(
        str_contains(
            $errorHtml,
            'value="Test cap nhat B04"'
        ),
        'Giu lai du lieu da nhap'
    );

    // TEST 15: Bao mat XSS

    $xssRequest = $postRequest->withParsedBody([
        '_token' => 'test-token',
        'sku' => '',
        'name' => '<script>alert(1)</script>'
    ]);

    $response = $controller->update(
        $xssRequest,
        ['id' => $id]
    );

    $xssHtml = (string) $response->getBody();

    check(
        $response->getStatusCode() === 422
        && !str_contains(
            $xssHtml,
            '<script>alert(1)</script>'
        )
        && str_contains(
            $xssHtml,
            '&lt;script&gt;alert(1)&lt;/script&gt;'
        ),
        'Bao mat XSS'
    );

    // TEST 16: Dieu chinh ton kho bang 0

    $stockRequest = new ServerRequest(
        'POST',
        '/admin/products/' . $id . '/stock'
    );

    $stockRequest = $stockRequest
        ->withAttribute('user', $admin)
        ->withAttribute('csrf_token', 'test-token')
        ->withParsedBody([
            '_token' => 'test-token',
            'quantity_change' => 0
        ]);

    $response = $controller->adjustStock(
        $stockRequest,
        ['id' => $id]
    );

    check(
        $response->getStatusCode() === 422,
        'Tu choi dieu chinh ton kho bang 0'
    );

    // TEST 17: CSRF khong hop le

    $invalidRequest = $stockRequest
        ->withParsedBody([
            '_token' => 'wrong-token',
            'quantity_change' => 10
        ]);

    $response = $controller->adjustStock(
        $invalidRequest,
        ['id' => $id]
    );

    check(
        $response->getStatusCode() === 403,
        'Tu choi CSRF khong hop le'
    );

    // TEST 18: ID khong hop le

    $response = $controller->edit(
        $request,
        ['id' => 0]
    );

    check(
        $response->getStatusCode() === 404,
        'Tu choi ID khong hop le'
    );

    // TEST 19: Chua dang nhap

    $guestRequest = new ServerRequest(
        'GET',
        '/admin/products/' . $id . '/edit'
    );

    $response = $controller->edit(
        $guestRequest,
        ['id' => $id]
    );

    check(
        $response->getStatusCode() === 401,
        'Tu choi nguoi chua dang nhap'
    );

    // TEST 20: Kiem tra du lieu khong bi thay doi

    $currentProduct = $model->find($id);

    check(
        $currentProduct !== null
        && $currentProduct['name'] === $product['name']
        && (int) $currentProduct['stock']
            === (int) $product['stock'],
        'Du lieu khong bi thay doi khi request loi'
    );

    echo PHP_EOL;

    echo 'PASS: All AdminProduct Edit tests'
        . PHP_EOL;

} catch (Throwable $e) {

    echo 'FAIL: '
        . $e->getMessage()
        . PHP_EOL;

    exit(1);
}
