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
