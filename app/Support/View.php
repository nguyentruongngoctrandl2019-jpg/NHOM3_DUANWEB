<?php
declare(strict_types=1);

namespace App\Support;

final class View
{
    // Hiển thị giao diện theo mô hình MVC
    public static function render(
        string $template,
        array $data = []
    ): void {
        $base = dirname(__DIR__) . '/Views/';

        $root = realpath($base);
        $file = realpath($base . $template . '.php');

        // Kiểm tra file giao diện có tồn tại không
        if (
            $root === false ||
            $file === false ||
            !str_starts_with(
                $file,
                $root . DIRECTORY_SEPARATOR
            )
        ) {
            throw new \RuntimeException(
                'Giao diện không tồn tại'
            );
        }

        // Chuyển dữ liệu thành biến cho View
        extract($data, EXTR_SKIP);
        $e = static fn(mixed $value): string => self::e($value);

$money = static fn(int|float|string $amount): string
    => self::money($amount);

        // Lưu nội dung trang vào biến $content
        ob_start();

        try {
            require $file;
            $content = (string) ob_get_clean();
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        // Đưa nội dung vào layout chính
        require $base . 'layouts/main.php';
    }

    // Chống XSS khi hiển thị dữ liệu
    public static function e(mixed $value): string
    {
        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );
    }

    // Định dạng giá tiền Việt Nam
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

declare(strict_types=1);

namespace App\Support;


final class View
{
    public static function render(string $template, array $data = []): string
    {
        $e = static fn(mixed $value): string =>
            htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $money = static fn(string $value): string => Money::display($value);
        $csrf = __DIR__ . '/../Views/layouts/csrf.php';

        extract($data, EXTR_SKIP);

        ob_start();

        try {
            require __DIR__ . '/../Views/' . $template . '.php';
            $content = ob_get_clean();
        } catch (\Throwable $error) {
            ob_end_clean();
            throw $error;
        }

        ob_start();

        try {
            require __DIR__ . '/../Views/layouts/main.php';

            return ob_get_clean();
        } catch (\Throwable $error) {
            ob_end_clean();
            throw $error;
        }
    }
}
