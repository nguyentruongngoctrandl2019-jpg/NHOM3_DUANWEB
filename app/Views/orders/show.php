<?php
$order = $order ?? null;
$phone = $phone ?? null;
$address = $address ?? null;

if (!is_array($order) || !is_string($phone) || !is_string($address)) {
    http_response_code(500);
    echo '<p>Chưa tải được chi tiết đơn hàng.</p>';
    return;
}
$e = static fn($value): string =>
    htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>

<main class="mx-auto max-w-4xl p-6">
    <a class="text-blue-700 underline" href="/orders">Về lịch sử đơn hàng</a>
    <h1 class="my-4 text-2xl font-bold">Chi tiết đơn hàng</h1>

    <p>Mã đơn: <?= $e($order['public_id']) ?></p>
    <p>Trạng thái: <?= $e($order['status']) ?></p>
    <p>Người nhận: <?= $e($order['customer_name']) ?></p>
    <p>Điện thoại: <?= $e($phone) ?></p>
    <p>Địa chỉ: <?= $e($address) ?></p>

    <h2 class="mt-4 text-lg font-semibold">Sản phẩm</h2>
    <ul class="list-inside list-disc">
        <?php foreach ($order['items'] as $item): ?>
            <li>
                Sản phẩm #<?= $e($item['product_id']) ?>,
                số lượng <?= $e($item['quantity']) ?>,
                đơn giá <?= number_format((int) $item['unit_price'], 0, ',', '.') ?> ₫
            </li>
        <?php endforeach; ?>
    </ul>

    <p class="mt-4 font-semibold">
        Tổng tiền: <?= number_format((int) $order['total'], 0, ',', '.') ?> ₫
    </p>
</main>