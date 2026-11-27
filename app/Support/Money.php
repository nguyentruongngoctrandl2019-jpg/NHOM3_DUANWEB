<?php

declare(strict_types=1);

namespace App\Support;

use InvalidArgumentException;

final class Money
{
    public const MAX = 999999999999999;

    public static function fromDecimal(string $value): int
    {
        if (!preg_match('/\A(\d{1,13})(?:\.(\d{1,2}))?\z/', $value, $parts)) {
            throw new InvalidArgumentException(
                'Giá phải là số không âm, tối đa 13 chữ số nguyên và 2 chữ số thập phân.'
            );
        }

        return (int) $parts[1] * 100 + (int) str_pad($parts[2] ?? '', 2, '0');
    }

    public static function toDecimal(int $value): string
    {
        if ($value < 0 || $value > self::MAX) {
            throw new InvalidArgumentException('Tổng tiền vượt giới hạn cho phép.');
        }

        return sprintf('%d.%02d', intdiv($value, 100), $value % 100);
    }

    public static function addLine(int $total, string $price, int $quantity): int
    {
        $unit = self::fromDecimal($price);

        if ($quantity < 1 || $unit > intdiv(self::MAX - $total, $quantity)) {
            throw new InvalidArgumentException('Tổng tiền vượt giới hạn cho phép.');
        }

        return $total + $unit * $quantity;
    }

    public static function display(string $price): string
    {
        $value = self::fromDecimal($price);

        return number_format(intdiv($value, 100), 0, ',', '.')
            . ($value % 100 ? sprintf(',%02d', $value % 100) : '') . ' ₫';
    }
}