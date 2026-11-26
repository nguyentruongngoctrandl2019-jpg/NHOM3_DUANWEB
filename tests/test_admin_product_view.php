
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

    $view = __DIR__
        . '/../app/Views/admin/products/index.php';

    check(
        is_file($view),
        'File index.php ton tai'
    );

    // TEST 4: Gia lap request quan tri

    $request = new ServerRequest(
        'GET',
        '/admin/products'
    );

    $request = $request
        ->withAttribute('user', [
            'id' => 1,
            'permissions' => ['products.manage']
        ])
        ->withAttribute('csrf_token', 'test-token');

    $response = $controller->index($request);

    check(
        $response->getStatusCode() === 200,
        'Trang quan ly san pham HTTP 200'
    );

    $html = (string) $response->getBody();

    // TEST 5: Tieu de

    check(
        str_contains($html, 'Quản lý sản phẩm'),
        'Hien thi tieu de'
    );

    // TEST 6: Bang san pham

    check(
        str_contains($html, '<table>'),
        'Hien thi bang san pham'
    );

    // TEST 7: Nut them san pham

    check(
        str_contains(
            $html,
            '/admin/products/create'
        ),
        'Nut them san pham'
    );

    // TEST 8: Form tim kiem

    check(
        str_contains($html, 'name="q"'),
        'Form tim kiem san pham'
    );

    // TEST 9: Hien thi du lieu MySQL

    $products = $model->all();

    if (!empty($products)) {

        $product = $products[0];

        $name = htmlspecialchars(
            (string) $product['name'],
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        check(
            str_contains($html, $name),
            'Hien thi san pham tu MySQL'
        );

        check(
            str_contains(
                $html,
                '/admin/products/'
                . (int) $product['id']
                . '/edit'
            ),
            'Nut chinh sua san pham'
        );

    } else {

        echo 'SKIP: Database chua co san pham'
            . PHP_EOL;
    }

    // TEST 10: CSRF

    $activeProducts = array_filter(
        $products,
        static fn(array $product): bool =>
            (int) $product['is_active'] === 1
    );

    if (!empty($activeProducts)) {

        check(
            str_contains($html, 'name="_token"')
            && str_contains($html, 'value="test-token"'),
            'CSRF token trong form ngung ban'
        );

    } else {

        echo 'SKIP: Chua co san pham dang kinh doanh'
            . PHP_EOL;
    }

    // TEST 11: Tim kiem khong co ket qua

    $searchRequest = $request->withQueryParams([
        'q' => 'NOTFOUND-' . bin2hex(random_bytes(8))
    ]);

    $response = $controller->index($searchRequest);

    $searchHtml = (string) $response->getBody();

    check(
        $response->getStatusCode() === 200,
        'Xu ly tim kiem'
    );

    check(
        str_contains(
            $searchHtml,
            'Không tìm thấy sản phẩm'
        ),
        'Hien thi danh sach rong'
    );

    // TEST 12: Bao mat XSS

    $xssRequest = $request->withQueryParams([
        'q' => '<script>alert(1)</script>'
    ]);

    $response = $controller->index($xssRequest);

    $xssHtml = (string) $response->getBody();

    check(
        !str_contains(
            $xssHtml,
            '<script>alert(1)</script>'
        )
        && str_contains(
            $xssHtml,
            '&lt;script&gt;alert(1)&lt;/script&gt;'
        ),
        'Bao mat XSS'
    );

    // TEST 13: Bao ve trang quan tri

    $guestRequest = new ServerRequest(
        'GET',
        '/admin/products'
    );

    $response = $controller->index($guestRequest);

    check(
        $response->getStatusCode() === 401,
        'Tu choi nguoi chua dang nhap'
    );

    echo PHP_EOL;

    echo 'PASS: All AdminProduct View tests'
        . PHP_EOL;

} catch (Throwable $e) {

    echo 'FAIL: '
        . $e->getMessage()
        . PHP_EOL;

    exit(1);
}
