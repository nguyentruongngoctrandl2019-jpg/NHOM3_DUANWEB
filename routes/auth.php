<?php

declare(strict_types=1);

return [];
namespace app\Support;

use app\Models\Permission;

final class Auth
{
    private static array $permissions = [];

    public static function userId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    public static function refresh(Permission $model): void
    {
        self::$permissions = [];
        $id = self::userId();

        if ($id === null) {
            return;
        }

        $permissions = $model->forUser($id);

        if ($permissions === null) {
            Session::signOut();
            return;
        }

        self::$permissions = $permissions;
    }

    public static function can(string $permission): bool
    {
        return in_array($permission, self::$permissions, true);
    }
}
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
