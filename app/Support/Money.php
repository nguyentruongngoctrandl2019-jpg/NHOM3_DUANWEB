<?php

declare(strict_types=1);

namespace App\Support;

use InvalidArgumentException;
use OverflowException;

final class Money
{
    public const MAX = 999999999999999;

    private const SCALE = 100;

    /**
     * Chuyen DECIMAL sang so nguyen.
     */
    public static function fromDecimal(string $value): int
    {
        $value = trim($value);

        if (!preg_match(
            '/\A(\d{1,13})(?:\.(\d{1,2}))?\z/',
            $value,
            $parts
        )) {
            throw new InvalidArgumentException(
                'Số tiền không hợp lệ.'
            );
        }

        $whole = (int) $parts[1];

        $fraction = (int) str_pad(
            $parts[2] ?? '',
            2,
            '0',
            STR_PAD_RIGHT
        );

        return $whole * self::SCALE + $fraction;
    }

    /**
     * Chuyen so nguyen sang DECIMAL.
     */
    public static function toDecimal(int $value): string
    {
        if ($value < 0 || $value > self::MAX) {
            throw new InvalidArgumentException(
                'Số tiền vượt giới hạn cho phép.'
            );
        }

        return sprintf(
            '%d.%02d',
            intdiv($value, self::SCALE),
            $value % self::SCALE
        );
    }

    /**
     * Dinh dang tien Viet Nam.
     */
    public static function formatVND(int $value): string
    {
        $decimal = self::toDecimal($value);

        [$whole, $fraction] = explode('.', $decimal);

        $formatted = number_format(
            (int) $whole,
            0,
            ',',
            '.'
        );

        if ($fraction !== '00') {
            $formatted .= ',' . $fraction;
        }

        return $formatted . ' ₫';
    }

    /**
     * Tinh tien theo so luong.
     */
    public static function multiply(
        int $price,
        int $quantity
    ): int {
        if (
            $price < 0
            || $price > self::MAX
            || $quantity < 0
        ) {
            throw new InvalidArgumentException(
                'Giá tiền hoặc số lượng không hợp lệ.'
            );
        }

        if ($quantity === 0) {
            return 0;
        }

        if ($price > intdiv(self::MAX, $quantity)) {
            throw new OverflowException(
                'Tổng tiền vượt giới hạn cho phép.'
            );
        }

        return $price * $quantity;
    }

    /**
     * Cong don tong tien don hang.
     */
    public static function addLine(
        int $total,
        string $price,
        int $quantity
    ): int {
        if (
            $total < 0
            || $total > self::MAX
            || $quantity < 1
        ) {
            throw new InvalidArgumentException(
                'Tổng tiền hoặc số lượng không hợp lệ.'
            );
        }

        $unit = self::fromDecimal($price);

        $lineTotal = self::multiply(
            $unit,
            $quantity
        );

        if ($lineTotal > self::MAX - $total) {
            throw new OverflowException(
                'Tổng tiền đơn hàng vượt giới hạn.'
            );
        }

        return $total + $lineTotal;
    }

    /**
     * Hien thi gia tien tu Database.
     */
    public static function display(string $price): string
    {
        return self::formatVND(
            self::fromDecimal($price)
        );
    }
}