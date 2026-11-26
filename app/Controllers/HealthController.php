<?php

declare(strict_types=1);

namespace app\Controllers;

use app\Support\Http;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\{ServerRequestInterface, ResponseInterface};

final class HealthController
{
    public function index(ServerRequestInterface $request, array $params): ResponseInterface
    {
        return new Response(200, ['Content-Type' => 'application/json'], '{"status":"ok"}');
    }

    public function home(ServerRequestInterface $request, array $params): ResponseInterface
    {
        return Http::redirect('/products');
    }
}