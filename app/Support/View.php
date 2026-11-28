<?php

declare(strict_types=1);

namespace App\Support;

use RuntimeException;
use Throwable;

final class View
{
    // ==========================================
    // 1. HIEN THI GIAO DIEN THEO MO HINH MVC
    // ==========================================

    public static function render(
        string $template,
        array $data = []
    ): string {

        // Xac dinh thu muc Views.
        $base = dirname(__DIR__) . '/Views/';

        $root = realpath($base);

        $file = realpath(
            $base . $template . '.php'
        );

        // Kiem tra file giao dien.
        if (
            $root === false
            || $file === false
            || !is_file($file)
            || !str_starts_with(
                $file,
                $root . DIRECTORY_SEPARATOR
            )
        ) {
            throw new RuntimeException(
                'Giao diện không tồn tại: ' . $template
            );
        }

        // Xac dinh file layout.
        $layout = $base . 'layouts/main.php';

        if (!is_file($layout)) {
            throw new RuntimeException(
                'Không tìm thấy layout main.php'
            );
        }

        // Duong dan file CSRF.
        $csrf = $base . 'layouts/csrf.php';

        // Ham chong XSS.
        $e = static fn(mixed $value): string
            => self::e($value);

        // Ham dinh dang tien.
        $money = static fn(
            int|float|string $amount
        ): string => self::money($amount);

        // Chuyen du lieu thanh bien cho View.
        extract($data, EXTR_SKIP);

        // ==========================================
        // 2. NAP NOI DUNG TRANG
        // ==========================================

        $bufferLevel = ob_get_level();

        ob_start();

        try {

            require $file;

            $content = (string) ob_get_clean();

        } catch (Throwable $error) {

            while (ob_get_level() > $bufferLevel) {
                ob_end_clean();
            }

            throw $error;
        }

        // ==========================================
        // 3. NAP LAYOUT CHINH
        // ==========================================

        $bufferLevel = ob_get_level();

        ob_start();

        try {

            require $layout;

            return (string) ob_get_clean();

        } catch (Throwable $error) {

            while (ob_get_level() > $bufferLevel) {
                ob_end_clean();
            }

            throw $error;
        }
    }

    // ==========================================
    // 4. CHONG XSS
    // ==========================================

    public static function e(
        mixed $value
    ): string {

        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );
    }

    // ==========================================
    // 5. DINH DANG TIEN VIET NAM
    // ==========================================

    public static function money(
        int|float|string $amount
    ): string {

        return number_format(
            (float) $amount,
            0,
            ',',
            '.'
        ) . ' ₫';
    }
}