<?php

declare(strict_types=1);
// Source - https://stackoverflow.com/a/21429652
// Posted by Fancy John, modified by community. See post 'Timeline' for change history
// Retrieved 2026-11-26, License - CC BY-SA 4.0

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');

if (str_starts_with($path, '/assets/') && !str_contains($path, "\0")) {
    $asset = realpath(__DIR__ . $path);
    $assetsDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR;

    if ($asset !== false
        && str_starts_with($asset, $assetsDirectory)
        && is_file($asset)
        && preg_match('/\.(css|svg|png|jpg|webp|ico)\z/i', $asset)
    ) {
        return false;
    }
}

require __DIR__ . '/index.php';