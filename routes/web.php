<?php

declare(strict_types=1);

use PhpDevCommunity\Route;
use PhpDevCommunity\Router;

// Khoi tao danh sach route.
$routes = [];
$metadata = [];
$registeredPaths = [];

// Lay tat ca file PHP trong thu muc routes.
$files = glob(__DIR__ . '/*.php') ?: [];

// Sap xep file.
sort($files, SORT_STRING);

foreach ($files as $file) {

    $fileName = basename($file);

    // Bo qua file web.php.
    if (strcasecmp($fileName, basename(__FILE__)) === 0) {
        continue;
    }

    // Nap danh sach route.
    $definitions = require $file;

    // Kiem tra du lieu route.
    if (!is_array($definitions)) {
        throw new RuntimeException(
            'Route file must return an array: ' . $fileName
        );
    }

    foreach ($definitions as $index => $definition) {

        // Kiem tra cac truong bat buoc.
        if (
            !is_array($definition)
            || !isset(
                $definition['name'],
                $definition['method'],
                $definition['path'],
                $definition['handler'],
                $definition['access']
            )
        ) {
            throw new RuntimeException(
                'Invalid route configuration: '
                . $fileName
                . ' - Index: '
                . $index
            );
        }

        // Lay thong tin route.
        $name = $definition['name'];
        $method = $definition['method'];
        $path = $definition['path'];
        $handler = $definition['handler'];
        $access = $definition['access'];

        // Kiem tra kieu du lieu.
        if (
            !is_string($name)
            || trim($name) === ''
            || !is_string($method)
            || !is_string($path)
            || !str_starts_with($path, '/')
            || !is_string($access)
            || trim($access) === ''
        ) {
            throw new RuntimeException(
                'Invalid route information: ' . $fileName
            );
        }

        // Chuan hoa HTTP Method.
        $method = strtoupper(trim($method));

        // Kiem tra HTTP Method.
        if (!in_array(
            $method,
            [
                'GET',
                'POST',
                'PUT',
                'PATCH',
                'DELETE',
                'HEAD',
                'OPTIONS'
            ],
            true
        )) {
            throw new RuntimeException(
                'Invalid HTTP method: ' . $method
            );
        }

        // Tao khoa kiem tra trung route.
        $key = $method . ' ' . $path;

        // Kiem tra trung ten route.
        if (isset($metadata[$name])) {
            throw new RuntimeException(
                'Duplicate route name: ' . $name
            );
        }

        // Kiem tra trung duong dan.
        if (isset($registeredPaths[$key])) {
            throw new RuntimeException(
                'Duplicate route path: ' . $key
            );
        }

        // Kiem tra handler.
        if (
            !is_array($handler)
            || count($handler) !== 2
            || !is_string($handler[0] ?? null)
            || !is_string($handler[1] ?? null)
        ) {
            throw new RuntimeException(
                'Invalid route handler: ' . $name
            );
        }

        // Kiem tra Controller.
        if (!class_exists($handler[0])) {
            throw new RuntimeException(
                'Controller not found: ' . $handler[0]
            );
        }

        // Kiem tra phuong thuc Controller.
        if (!method_exists($handler[0], $handler[1])) {
            throw new RuntimeException(
                'Controller method not found: '
                . $handler[0]
                . '::'
                . $handler[1]
            );
        }

        // Khoi tao Route.
        $route = new Route(
            $name,
            $path,
            $handler,
            [$method]
        );

        // Them route vao danh sach.
        $routes[] = $route;

        // Luu thong tin route.
        $metadata[$name] = [
            'name' => $name,
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'access' => $access,
            'route' => $route,
        ];

        // Danh dau route da duoc dang ky.
        $registeredPaths[$key] = true;
    }
}

// Lay duong dan co so.
$baseUrl = getenv('APP_BASE_URL') ?: '';

// Khoi tao Router.
$router = new Router(
    $routes,
    $baseUrl
);

// Tra ve cau hinh Router.
return [
    'router' => $router,
    'metadata' => $metadata,
    'base_url' => $baseUrl,
];