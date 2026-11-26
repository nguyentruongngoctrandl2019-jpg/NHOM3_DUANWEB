<?php

declare(strict_types=1);

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