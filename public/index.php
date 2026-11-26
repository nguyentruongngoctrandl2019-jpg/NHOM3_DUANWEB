<?php

declare(strict_types=1);

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