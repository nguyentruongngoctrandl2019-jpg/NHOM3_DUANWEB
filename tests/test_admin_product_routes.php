
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

    // TEST 1: Kiem tra file Route

    $file = __DIR__
        . '/../routes/admin-products.php';

    check(
        is_file($file),
        'File admin-products.php ton tai'
    );

    // TEST 2: Doc danh sach Route

    $routes = require $file;

    check(
        is_array($routes),
        'Route tra ve mang'
    );

    check(
        count($routes) === 7,
        'Co du 7 Route quan tri san pham'
    );

    // TEST 3: Kiem tra cau truc Route

    $requiredKeys = [
        'name',
        'method',
        'path',
        'handler',
        'access'
    ];

    $names = [];
    $signatures = [];

    foreach ($routes as $route) {

        foreach ($requiredKeys as $key) {

            check(
                array_key_exists($key, $route),
                $route['name'] . ' co key ' . $key
            );
        }

        check(
            in_array(
                $route['method'],
                ['GET', 'POST'],
                true
            ),
            $route['name'] . ' HTTP method hop le'
        );

        check(
            $route['access'] === 'products.manage',
            $route['name'] . ' dung quyen truy cap'
        );

        check(
            is_array($route['handler'])
            && count($route['handler']) === 2,
            $route['name'] . ' handler hop le'
        );

        check(
            $route['handler'][0]
                === AdminProductController::class,
            $route['name'] . ' dung Controller'
        );

        check(
            method_exists(
                $route['handler'][0],
                $route['handler'][1]
            ),
            $route['name'] . ' phuong thuc ton tai'
        );

        $names[] = $route['name'];

        $signatures[] = $route['method']
            . ' '
            . $route['path'];
    }

    // TEST 4: Khong trung ten Route

    check(
        count($names) === count(array_unique($names)),
        'Khong trung ten Route'
    );

    // TEST 5: Khong trung HTTP method va path

    check(
        count($signatures)
            === count(array_unique($signatures)),
        'Khong trung method va path'
    );

    // TEST 6: Kiem tra day du 7 Route

    $expected = [

        'GET /admin/products'
            => 'index',

        'GET /admin/products/create'
            => 'create',

        'POST /admin/products/create'
            => 'store',

        'GET /admin/products/{id}/edit'
            => 'edit',

        'POST /admin/products/{id}/edit'
            => 'update',

        'POST /admin/products/{id}/disable'
            => 'disable',

        'POST /admin/products/{id}/stock'
            => 'adjustStock'
    ];

    $actual = [];

    foreach ($routes as $route) {

        $key = $route['method']
            . ' '
            . $route['path'];

        $actual[$key] = $route['handler'][1];
    }

    foreach ($expected as $path => $method) {

        check(
            isset($actual[$path])
            && $actual[$path] === $method,
            $path . ' -> ' . $method
        );
    }

    // TEST 7: Ket noi PDO

    $pdo = Database::connect();

    check(
        $pdo instanceof PDO,
        'Ket noi PDO'
    );

    // TEST 8: Khoi tao Model va Controller

    $model = new AdminProduct($pdo);

    $controller = new AdminProductController($model);

    check(
        $controller instanceof AdminProductController,
        'Khoi tao AdminProductController'
    );

    // TEST 9: Gia lap tai khoan quan tri

    $admin = [
        'id' => 1,
        'permissions' => ['products.manage']
    ];

    // TEST 10: Trang danh sach

    $request = new ServerRequest(
        'GET',
        '/admin/products'
    );

    $request = $request
        ->withAttribute('user', $admin)
        ->withAttribute('csrf_token', 'test-token');

    $response = $controller->index($request);

    check(
        $response instanceof ResponseInterface,
        'Response hop le'
    );

    check(
        $response->getStatusCode() === 200,
        'Trang danh sach HTTP 200'
    );

    // TEST 11: Trang them san pham

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

    // TEST 12: Trang chinh sua

    $products = $model->all();

    if (!empty($products)) {

        $id = (int) $products[0]['id'];

        $request = new ServerRequest(
            'GET',
            '/admin/products/' . $id . '/edit'
        );

        $request = $request
            ->withAttribute('user', $admin)
            ->withAttribute('csrf_token', 'test-token');

        $response = $controller->edit(
            $request,
            ['id' => $id]
        );

        check(
            $response->getStatusCode() === 200,
            'Trang chinh sua HTTP 200'
        );

    } else {

        echo 'SKIP: Database chua co san pham'
            . PHP_EOL;
    }

    // TEST 13: Bao ve tat ca Route

    foreach ($routes as $route) {

        $method = $route['handler'][1];

        $path = str_replace(
            '{id}',
            '1',
            $route['path']
        );

        $request = new ServerRequest(
            $route['method'],
            $path
        );

        $response = $controller->$method(
            $request,
            ['id' => 1]
        );

        check(
            $response->getStatusCode() === 401,
            $route['name'] . ' tu choi guest'
        );

        // Nguoi dung khong co quyen

        $request = $request->withAttribute(
            'user',
            [
                'id' => 1,
                'permissions' => []
            ]
        );

        $response = $controller->$method(
            $request,
            ['id' => 1]
        );

        check(
            $response->getStatusCode() === 403,
            $route['name'] . ' tu choi sai quyen'
        );
    }

    // TEST 14: Kiem tra CSRF cua cac Route POST

    foreach ($routes as $route) {

        if ($route['method'] !== 'POST') {
            continue;
        }

        $method = $route['handler'][1];

        $path = str_replace(
            '{id}',
            '1',
            $route['path']
        );

        $request = new ServerRequest(
            'POST',
            $path
        );

        $request = $request
            ->withAttribute('user', $admin)
            ->withAttribute('csrf_token', 'test-token')
            ->withParsedBody([
                '_token' => 'wrong-token'
            ]);

        $response = $controller->$method(
            $request,
            ['id' => 1]
        );

        check(
            $response->getStatusCode() === 403,
            $route['name'] . ' bao ve CSRF'
        );
    }

    echo PHP_EOL;

    echo 'PASS: All AdminProduct Route tests'
        . PHP_EOL;

} catch (Throwable $e) {

    echo 'FAIL: '
        . $e->getMessage()
        . PHP_EOL;

    exit(1);
}
