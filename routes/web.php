<?php

declare(strict_types=1);

use PhpDevCommunity\Route;
use PhpDevCommunity\Router;

$routes = [];
$metadata = [];
$registeredPaths = [];

$files = glob(__DIR__ . '/*.php') ?: [];

sort($files, SORT_STRING);

foreach ($files as $file) {

    if (basename($file) === 'web.php') {
        continue;
    }

    $definitions = require $file;

    if (!is_array($definitions)) {
        throw new RuntimeException(
            'Route file must return an array: ' . $file
        );
    }

    foreach ($definitions as $definition) {

        if (
            !isset(
                $definition['name'],
                $definition['method'],
                $definition['path'],
                $definition['handler'],
                $definition['access']
            )
        ) {
            throw new RuntimeException(
                'Invalid route configuration: ' . $file
            );
        }

        $name = $definition['name'];
        $method = strtoupper($definition['method']);
        $path = $definition['path'];
        $handler = $definition['handler'];
        $access = $definition['access'];

        $key = $method . ' ' . $path;

        if (isset($metadata[$name])) {
            throw new RuntimeException(
                'Duplicate route name: ' . $name
            );
        }

        if (isset($registeredPaths[$key])) {
            throw new RuntimeException(
                'Duplicate route path: ' . $key
            );
        }

        if (
            !is_array($handler) ||
            count($handler) !== 2
        ) {
            throw new RuntimeException(
                'Invalid route handler: ' . $name
            );
        }

        $route = new Route(
            $name,
            $path,
            $handler,
            [$method]
        );

        $routes[] = $route;

        $metadata[$name] = [
            'name' => $name,
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'access' => $access,
            'route' => $route,
        ];

        $registeredPaths[$key] = true;
    }
}

$baseUrl = getenv('APP_BASE_URL') ?: '';

return [
    'router' => new Router($routes, $baseUrl),
    'metadata' => $metadata,
    'base_url' => $baseUrl,
$routes = $metadata = $paths = [];

$files = glob(__DIR__ . '/*.php');
sort($files, SORT_STRING);

foreach ($files as $file) {
    $fileName = basename($file);

    if (strcasecmp($fileName, basename(__FILE__)) === 0) {
        continue;
    }

    $definitions = require $file;
    
    $fileName = basename($file);

    if (!is_array($definitions)) {
        throw new LogicException(
            "File route {$fileName} phải dùng return [...]. "
            . 'Kiểu nhận được: ' . get_debug_type($definitions)
        );
    }

    foreach ($definitions as $index => $definition) {
        if (!is_array($definition)
            || !isset(
                $definition['name'],
                $definition['path'],
                $definition['method'],
                $definition['handler'],
                $definition['access']
            )
        ) {
            throw new LogicException(
                "File {$fileName}, mục {$index}: khai báo route thiếu trường."
            );
        }

        [
            'name' => $name,
            'path' => $path,
            'method' => $method,
            'handler' => $handler,
            'access' => $access,
        ] = $definition;

        if (!is_string($name) || $name === ''
            || !is_string($path) || !str_starts_with($path, '/')
            || !in_array($method, ['GET', 'POST'], true)
            || !is_string($access)
            || !preg_match('/\A(public|login|[a-z]+\.manage)\z/', $access)
        ) {
            throw new LogicException(
                "File {$fileName}, mục {$index}: tên, đường dẫn, method hoặc quyền không hợp lệ."
            );
        }

        if (isset($metadata[$name]) || isset($paths[$method . $path])) {
            throw new LogicException("File {$fileName}: route bị trùng: {$name}.");
        }

        if (!is_array($handler)
            || count($handler) !== 2
            || !is_string($handler[0] ?? null)
            || !is_string($handler[1] ?? null)
            || !method_exists($handler[0], $handler[1])
        ) {
            throw new LogicException(
                "File {$fileName}, route {$name}: kiểm tra class và tên hàm trong handler."
            );
        }

        $metadata[$name] = $access;
        $paths[$method . $path] = true;
        $routes[] = new Route($name, $path, $handler, [$method]);
    }
}

return [
    new Router($routes, getenv('APP_URL') ?: 'http://127.0.0.1:8001'),
    $routes,
    $metadata,
];