<?php

declare(strict_types=1);

use App\Controllers\AuthController;

return [
    [
        'name' => 'login',
        'method' => 'GET',
        'path' => '/login',
        'handler' => [AuthController::class, 'login'],
        'access' => 'public',
    ],
    [
        'name' => 'login.submit',
        'method' => 'POST',
        'path' => '/login',
        'handler' => [AuthController::class, 'login'],
        'access' => 'public',
    ],
    [
        'name' => 'register',
        'method' => 'GET',
        'path' => '/register',
        'handler' => [AuthController::class, 'register'],
        'access' => 'public',
    ],
    [
        'name' => 'register.submit',
        'method' => 'POST',
        'path' => '/register',
        'handler' => [AuthController::class, 'register'],
        'access' => 'public',
    ],
    [
        'name' => 'logout',
        'method' => 'POST',
        'path' => '/logout',
        'handler' => [AuthController::class, 'logout'],
        'access' => 'login',
    ],
];