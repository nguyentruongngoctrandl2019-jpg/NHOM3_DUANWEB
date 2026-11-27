<?php

declare(strict_types=1);

use App\Controllers\AuthController;

return [
    [
        'name' => 'auth.login',
        'method' => 'GET',
        'path' => '/login',
        'handler' => [AuthController::class, 'login'],
        'access' => 'public',
    ],
    [
        'name' => 'auth.login.submit',
        'method' => 'POST',
        'path' => '/login',
        'handler' => [AuthController::class, 'login'],
        'access' => 'public',
    ],
    [
        'name' => 'auth.register',
        'method' => 'GET',
        'path' => '/register',
        'handler' => [AuthController::class, 'register'],
        'access' => 'public',
    ],
    [
        'name' => 'auth.register.submit',
        'method' => 'POST',
        'path' => '/register',
        'handler' => [AuthController::class, 'register'],
        'access' => 'public',
    ],
    [
        'name' => 'auth.logout',
        'method' => 'POST',
        'path' => '/logout',
        'handler' => [AuthController::class, 'logout'],
        'access' => 'login',
    ],
];