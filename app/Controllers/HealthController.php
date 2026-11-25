<?php

declare(strict_types=1);

namespace App\Controllers;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class HealthController
{
    public function index(
        ServerRequestInterface $request,
        array $params = []
    ): ResponseInterface {

        $data = [
            'status' => 'ok',
            'project' => 'CT Tech Store',
            'message' => 'Router va MVC hoat dong',
            'php_version' => PHP_VERSION,
            'query' => $request->getQueryParams()
        ];

        return new Response(
            200,
            ['Content-Type' => 'application/json; charset=utf-8'],
            json_encode(
                $data,
                JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
            )
        );
    }
}