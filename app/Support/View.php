<?php

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