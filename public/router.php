<?php

declare(strict_types=1);

$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');

if (str_starts_with($path, '/assets/') && !str_contains($path, "\0")) {
    $asset = realpath(__DIR__ . $path);

    if (
        $asset !== false
        && str_starts_with($asset, __DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR)
        && is_file($asset)
        && preg_match('/\.(css|svg|png|jpg|webp|ico)\z/i', $asset)
    ) {
        return false;
    }
}

require __DIR__ . '/index.php';