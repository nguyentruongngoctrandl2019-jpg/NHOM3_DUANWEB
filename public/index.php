<?php

declare(strict_types=1);

use App\Support\Kernel;
use GuzzleHttp\Psr7\ServerRequest;

$root = dirname(__DIR__);

$autoload = $root . '/vendor/autoload.php';

if (!is_file($autoload)) {

    http_response_code(500);

    exit(
        'Composer dependencies are missing. Run composer install.'
    );
}

require $autoload;

date_default_timezone_set('Asia/Ho_Chi_Minh');

try {

    // Khoi tao session truoc khi gui output.
    if (session_status() === PHP_SESSION_NONE) {

        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => (
                !empty($_SERVER['HTTPS']) &&
                $_SERVER['HTTPS'] !== 'off'
            ),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        session_start();
    }

    // Nap danh sach route.
    $config = require $root . '/routes/web.php';

    // Factory cho cac controller can dependency.
    // Se bo sung khi tich hop A04, B02, B03...
    $factories = [];

    // Khoi tao Kernel.
    $kernel = new Kernel(
        $config['router'],
        $config['metadata'],
        $config['base_url'],
        $factories
    );

    // Tao request theo chuan PSR-7.
    $request = ServerRequest::fromGlobals();

    // Xu ly request.
    $response = $kernel->handle($request);

    // Gui response ve trinh duyet.
    $kernel->send($response);

} catch (Throwable $e) {

    error_log(
        get_class($e) . ': ' . $e->getMessage()
    );

    http_response_code(500);

    header(
        'Content-Type: application/json; charset=utf-8'
    );

    echo json_encode([
        'status' => 500,
        'message' => 'Application initialization failed'
    ]);
use app\Support\{Http, Kernel, Session};
use GuzzleHttp\Psr7\ServerRequest;

if (!is_file(__DIR__ . '/../vendor/autoload.php')) {
    http_response_code(503);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('Hãy chạy composer install trong thư mục dự án.');
}

require __DIR__ . '/../vendor/autoload.php';

ini_set('display_errors', '0');

try {
    Session::start();
    $request = ServerRequest::fromGlobals();
    $response = (new Kernel())->handle($request);
} catch (Throwable $error) {
    $driverCode = $error instanceof PDOException
        ? (int) ($error->errorInfo[1] ?? 0)
        : 0;

    error_log(
        'CT Tech Store: ' . get_class($error)
        . ' [' . $error->getCode() . '/' . $driverCode . '] at '
        . basename($error->getFile()) . ':' . $error->getLine()
    );

    $response = Http::error(500, 'Hệ thống đang gặp lỗi. Vui lòng thử lại sau.');
}

if (session_status() === PHP_SESSION_ACTIVE) {
    session_write_close();
}

http_response_code($response->getStatusCode());

foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header($name . ': ' . $value, false);
    }
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'HEAD') {
    echo $response->getBody();
}