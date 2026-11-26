<?php

declare(strict_types=1);

namespace app\Support;

final class Session
{
    public static function start(): void
    {
        ini_set('session.use_strict_mode', '1');

        session_set_cookie_params([
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        ]);

        if (!session_start()) {
            throw new \RuntimeException('Unable to start session');
        }
    }

    public static function signIn(int $id): void
    {
        session_regenerate_id(true);
        $_SESSION = ['user_id' => $id];
    }

    public static function signOut(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
    }
}