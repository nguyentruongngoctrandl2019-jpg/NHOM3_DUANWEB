<?php

declare(strict_types=1);

namespace App\Support;

use Psr\Http\Message\ServerRequestInterface;

final class Input
{
    public static function text(array $data, string $field): string
    {
        $value = $data[$field] ?? null;

        return is_string($value) && mb_check_encoding($value, 'UTF-8')
            ? trim($value)
            : '';
    }

    public static function positive(mixed $value, int $max = PHP_INT_MAX): ?int
    {
        if ((!is_string($value) && !is_int($value))
            || !preg_match('/\A[1-9][0-9]*\z/', (string) $value)
        ) {
            return null;
        }

        $number = filter_var($value, FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => 1,
                'max_range' => $max,
            ],
        ]);

        return $number === false ? null : $number;
    }

    public static function form(ServerRequestInterface $request): array
    {
        return (array) $request->getParsedBody();
    }
}