<?php

declare(strict_types=1);

namespace app\Support;

use GuzzleHttp\Psr7\Response;
use app\Support\View;

final class Http
{
    public static function view(
        string $template,
        array $data = [],
        int $status = 200,
        array $headers = []
    ): Response {
        return new Response(
            $status,
            ['Content-Type' => 'text/html; charset=UTF-8', 'Cache-Control' => 'no-store'] + $headers,
            View::render($template, $data)
        );
    }

    public static function redirect(string $location): Response
    {
        return new Response(303, [
            'Location' => $location,
            'Cache-Control' => 'no-store',
        ]);
    }

    public static function error(int $status, string $message, array $headers = []): Response
    {
        return self::view('layouts/error', compact('status', 'message'), $status, $headers);
    }
}