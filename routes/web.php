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
];