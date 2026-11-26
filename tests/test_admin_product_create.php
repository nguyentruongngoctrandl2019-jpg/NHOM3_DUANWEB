
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
        . '/../app/Views/admin/products/create.php';

    check(
        is_file($file),
        'File create.php ton tai'
    );

    // TEST 4: Gia lap request quan tri

    $admin = [
        'id' => 1,
        'permissions' => ['products.manage']
    ];

    $request = new ServerRequest(
        'GET',
        '/admin/products/create'
    );

    $request = $request
        ->withAttribute('user', $admin)
        ->withAttribute('csrf_token', 'test-token');

    $response = $controller->create($request);

    check(
        $response->getStatusCode() === 200,
        'Trang them san pham HTTP 200'
    );

    $html = (string) $response->getBody();

    // TEST 5: Kiem tra tieu de

    check(
        str_contains($html, 'Thêm sản phẩm mới'),
        'Hien thi tieu de'
    );

    // TEST 6: Kiem tra form

    check(
        str_contains($html, 'method="POST"')
        && str_contains(
            $html,
            'action="/admin/products/create"'
        ),
        'Form them san pham hop le'
    );

    // TEST 7: Kiem tra cac truong

    $fields = [
        'sku',
        'name',
        'category_id',
        'price',
        'warranty_months',
        'stock',
        'description',
        'is_active',
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

    // TEST 8: Kiem tra danh muc tu MySQL

    $categories = $model->categories();

    if (!empty($categories)) {

        $category = $categories[0];

        $categoryName = htmlspecialchars(
            (string) $category['name'],
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        check(
            str_contains($html, $categoryName),
            'Hien thi danh muc tu MySQL'
        );

    } else {

        echo 'SKIP: Chua co danh muc'
            . PHP_EOL;
    }

    // TEST 9: Kiem tra CSRF

    check(
        str_contains($html, 'value="test-token"'),
        'Hien thi CSRF token'
    );

    // TEST 10: Kiem tra nut luu

    check(
        str_contains($html, 'Lưu sản phẩm'),
        'Hien thi nut luu san pham'
    );

    // TEST 11: Du lieu khong hop le

    $postRequest = new ServerRequest(
        'POST',
        '/admin/products/create'
    );

    $postRequest = $postRequest
        ->withAttribute('user', $admin)
        ->withAttribute('csrf_token', 'test-token')
        ->withParsedBody([
            '_token' => 'test-token',
            'sku' => '',
            'name' => 'San pham test B04',
            'category_id' => 0,
            'price' => '-100',
            'warranty_months' => 12,
            'stock' => 10,
            'description' => 'Test validation',
            'is_active' => 1
        ]);

    $response = $controller->store($postRequest);

    check(
        $response->getStatusCode() === 422,
        'Tu choi du lieu khong hop le'
    );

    $errorHtml = (string) $response->getBody();

    check(
        str_contains(
            $errorHtml,
            'Không thể lưu sản phẩm!'
        ),
        'Hien thi thong bao loi'
    );

    check(
        str_contains(
            $errorHtml,
            'value="San pham test B04"'
        ),
        'Giu lai du lieu da nhap'
    );

    // TEST 12: Bao mat XSS

    $xssRequest = $postRequest->withParsedBody([
        '_token' => 'test-token',
        'sku' => '',
        'name' => '<script>alert(1)</script>'
    ]);

    $response = $controller->store($xssRequest);

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

    // TEST 13: CSRF sai

    $invalidRequest = $postRequest->withParsedBody([
        '_token' => 'wrong-token'
    ]);

    $response = $controller->store($invalidRequest);

    check(
        $response->getStatusCode() === 403,
        'Tu choi CSRF khong hop le'
    );

    // TEST 14: Chua dang nhap

    $guestRequest = new ServerRequest(
        'GET',
        '/admin/products/create'
    );

    $response = $controller->create($guestRequest);

    check(
        $response->getStatusCode() === 401,
        'Tu choi nguoi chua dang nhap'
    );

    echo PHP_EOL;

    echo 'PASS: All AdminProduct Create tests'
        . PHP_EOL;

} catch (Throwable $e) {

    echo 'FAIL: '
        . $e->getMessage()
        . PHP_EOL;

    exit(1);
}
