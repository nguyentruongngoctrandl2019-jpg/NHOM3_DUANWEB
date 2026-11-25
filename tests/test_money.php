<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/Support/Money.php';

use App\Support\Money;

function check(
    mixed $actual,
    mixed $expected,
    string $message
): void {
    if ($actual !== $expected) {
        echo "FAIL: " . $message . PHP_EOL;

        echo "Expected: "
            . var_export($expected, true)
            . PHP_EOL;

        echo "Actual: "
            . var_export($actual, true)
            . PHP_EOL;

        exit(1);
    }

    echo "PASS: " . $message . PHP_EOL;
}

// TEST 1: DECIMAL sang Integer

check(
    Money::fromDecimal('100000.00'),
    10000000,
    'Decimal to Integer'
);

// TEST 2: Integer sang DECIMAL

check(
    Money::toDecimal(10000000),
    '100000.00',
    'Integer to Decimal'
);

// TEST 3: Tien co phan thap phan

check(
    Money::fromDecimal('150000.50'),
    15000050,
    'Decimal precision'
);

// TEST 4: So tien bang 0

check(
    Money::fromDecimal('0.00'),
    0,
    'Zero amount'
);

// TEST 5: So tien am

check(
    Money::fromDecimal('-100.50'),
    -10050,
    'Negative amount'
);

// TEST 6: Dinh dang VND

check(
    Money::formatVND(599000000),
    '5.990.000 đ',
    'Format VND'
);

// TEST 7: Tinh tong tien

check(
    Money::multiply(599000000, 2),
    1198000000,
    'Calculate total'
);

// TEST 8: Quy doi nguoc

check(
    Money::toDecimal(
        Money::fromDecimal('15990000.25')
    ),
    '15990000.25',
    'Round trip conversion'
);

// TEST 9: Kiem tra gia tri khong hop le

try {
    Money::fromDecimal('abc');

    echo "FAIL: Invalid amount" . PHP_EOL;
    exit(1);

} catch (InvalidArgumentException $e) {
    echo "PASS: Invalid amount" . PHP_EOL;
}

// TEST 10: Kiem tra vuot gioi han

try {
    Money::fromDecimal('10000000000000.00');

    echo "FAIL: Amount overflow" . PHP_EOL;
    exit(1);

} catch (OverflowException $e) {
    echo "PASS: Amount overflow" . PHP_EOL;
}

echo PHP_EOL;
echo "PASS: All Money tests" . PHP_EOL;