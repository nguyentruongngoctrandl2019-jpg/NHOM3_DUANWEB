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
    if ($file === __FILE__) {
        continue;
    }

    foreach (require $file as $definition) {
        [
            'name' => $name,
            'path' => $path,
            'method' => $method,
            'handler' => $handler,
            'access' => $access,
        ] = $definition;

        if (isset($metadata[$name])
            || isset($paths[$method . $path])
            || !in_array($method, ['GET', 'POST'], true)
            || !str_starts_with($path, '/')
            || !method_exists(...$handler)
            || !preg_match('/\A(public|login|[a-z]+\.manage)\z/', $access)
        ) {
            throw new LogicException('Invalid or duplicate route: ' . $name);
        }

        $metadata[$name] = $access;
        $paths[$method . $path] = true;
        $routes[] = new Route($name, $path, $handler, [$method]);
    }
}

return [
    new Router($routes, getenv('APP_URL') ?: 'http://localhost:8000'),
    $routes,
    $metadata,
];