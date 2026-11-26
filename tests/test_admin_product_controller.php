
<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Support\Database;
use App\Models\AdminProduct;
use App\Controllers\AdminProductController;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;

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

    // TEST 2: Khoi tao Model

    $model = new AdminProduct($pdo);

    check(
        $model instanceof AdminProduct,
        'Khoi tao AdminProduct Model'
    );

    // TEST 3: Khoi tao Controller

    $controller = new AdminProductController($model);

    check(
        $controller instanceof AdminProductController,
        'Khoi tao AdminProductController'
    );

    // TEST 4: Kiem tra cac phuong thuc

    $methods = [
        'index',
        'create',
        'store',
        'edit',
        'update',
        'disable',
        'adjustStock'
    ];

    foreach ($methods as $method) {

        check(
            is_callable([$controller, $method]),
            'Phuong thuc ' . $method
        );
    }

    // TEST 5: Chua dang nhap

    $request = new ServerRequest(
        'GET',
        '/admin/products'
    );

    $response = $controller->index($request);

    check(
        $response instanceof ResponseInterface,
        'Response hop le'
    );

    check(
        $response->getStatusCode() === 401,
        'Tu choi nguoi chua dang nhap'
    );

    // TEST 6: Khong co quyen

    $request = $request->withAttribute('user', [
        'id' => 1,
        'permissions' => []
    ]);

    $response = $controller->index($request);

    check(
        $response->getStatusCode() === 403,
        'Tu choi nguoi khong co quyen'
    );

    // Tao request quan tri gia lap

    $admin = [
        'id' => 1,
        'permissions' => ['products.manage']
    ];

    // TEST 7: ID khong hop le

    $request = new ServerRequest(
        'GET',
        '/admin/products/0/edit'
    );

    $request = $request->withAttribute(
        'user',
        $admin
    );

    $response = $controller->edit(
        $request,
        ['id' => 0]
    );

    check(
        $response->getStatusCode() === 404,
        'Tu choi ID khong hop le'
    );

    // TEST 8: ID khong phai so

    $response = $controller->edit(
        $request,
        ['id' => 'abc']
    );

    check(
        $response->getStatusCode() === 404,
        'Tu choi ID khong phai so'
    );

    // TEST 9: CSRF khong hop le

    $request = new ServerRequest(
        'POST',
        '/admin/products/create'
    );

    $request = $request
        ->withAttribute('user', $admin)
        ->withAttribute('csrf_token', 'test-token')
        ->withParsedBody([
            '_token' => 'wrong-token'
        ]);

    $response = $controller->store($request);

    check(
        $response->getStatusCode() === 403,
        'Tu choi CSRF khong hop le'
    );

    // TEST 10: Thieu CSRF

    $request = $request->withParsedBody([]);

    $response = $controller->store($request);

    check(
        $response->getStatusCode() === 403,
        'Tu choi request thieu CSRF'
    );

    // TEST 11: CSRF dung, ID sai

    $request = new ServerRequest(
        'POST',
        '/admin/products/0/disable'
    );

    $request = $request
        ->withAttribute('user', $admin)
        ->withAttribute('csrf_token', 'test-token')
        ->withParsedBody([
            '_token' => 'test-token'
        ]);

    $response = $controller->disable(
        $request,
        ['id' => 0]
    );

    check(
        $response->getStatusCode() === 404,
        'CSRF hop le nhung ID khong hop le'
    );

    // TEST 12: Bao ve tat ca phuong thuc POST

    $request = new ServerRequest(
        'POST',
        '/admin/products'
    );

    foreach ([
        'store',
        'update',
        'disable',
        'adjustStock'
    ] as $method) {

        $response = $controller->$method(
            $request,
            ['id' => 1]
        );

        check(
            $response->getStatusCode() === 401,
            'Bao ve POST ' . $method
        );
    }

    echo PHP_EOL;

    echo 'PASS: All AdminProductController tests'
        . PHP_EOL;

} catch (Throwable $e) {

    echo 'FAIL: '
        . $e->getMessage()
        . PHP_EOL;

    exit(1);
}
