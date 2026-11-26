<?php

declare(strict_types=1);

use PhpDevCommunity\Route;
use PhpDevCommunity\Router;

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