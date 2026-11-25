<?php

declare(strict_types=1);

use App\Controllers\HealthController;

return [
    [
        'name' => 'health.index',
        'method' => 'GET',
        'path' => '/health',
        'handler' => [
            HealthController::class,
            'index'
        ],
        'access' => 'public',
    ],
];