<?php

declare(strict_types=1);

namespace App\Support;

use InvalidArgumentException;
use OverflowException;

class Money
{
    // Ty le quy doi tien
    private const SCALE = 100;

    // Gioi han cua DECIMAL(15,2)
    private const MAX_AMOUNT = 999999999999999;

    /**
     * Chuyen DECIMAL sang so nguyen.
     * Vi du: "150000.50" => 15000050
     */
    public static function fromDecimal(string $amount): int
    {
        $amount = trim($amount);

        if (!preg_match(
            '/\A-?\d+(?:\.\d{1,2})?\z/',
            $amount
        )) {
            throw new InvalidArgumentException(
                'So tien khong hop le'
            );
        }

        $negative = str_starts_with($amount, '-');

        $amount = ltrim($amount, '-');

        $parts = explode('.', $amount, 2);

        $whole = ltrim($parts[0], '0');

        if (strlen($whole) > 13) {
            throw new OverflowException(
                'So tien vuot qua gioi han'
            );
        }

        $integer = (int) $whole;

        $decimal = str_pad(
            $parts[1] ?? '0',
            2,
            '0',
            STR_PAD_RIGHT
        );

        $result = $integer * self::SCALE
                + (int) $decimal;

        return $negative ? -$result : $result;
    }

    /**
     * Chuyen so nguyen ve DECIMAL.
     * Vi du: 15000050 => "150000.50"
     */
    public static function toDecimal(int $amount): string
    {
        if (
            $amount > self::MAX_AMOUNT ||
            $amount < -self::MAX_AMOUNT
        ) {
            throw new OverflowException(
                'So tien vuot qua gioi han'
            );
        }

        $negative = $amount < 0;

        $amount = abs($amount);

        $whole = intdiv($amount, self::SCALE);

        $decimal = $amount % self::SCALE;

        return ($negative ? '-' : '')
            . $whole
            . '.'
            . str_pad(
                (string) $decimal,
                2,
                '0',
                STR_PAD_LEFT
            );
    }

    /**
     * Dinh dang tien Viet Nam.
     * Vi du: 15000000 => "150.000 đ"
     */
    public static function formatVND(int $amount): string
    {
        $decimal = self::toDecimal($amount);

        [$whole, $fraction] = explode('.', $decimal);

        $formatted = preg_replace(
            '/\B(?=(\d{3})+(?!\d))/',
            '.',
            $whole
        );

        if ($fraction !== '00') {
            $formatted .= ',' . $fraction;
        }

        return $formatted . ' đ';
    }

    /**
     * Tinh tong tien theo so luong.
     */
    public static function multiply(
        int $price,
        int $quantity
    ): int {
        if ($price < 0 || $quantity < 0) {
            throw new InvalidArgumentException(
                'Gia tien hoac so luong khong hop le'
            );
        }

        if (
            $quantity > 0 &&
            $price > intdiv(self::MAX_AMOUNT, $quantity)
        ) {
            throw new OverflowException(
                'Tong tien vuot qua gioi han'
            );
        }

        return $price * $quantity;
    }
}