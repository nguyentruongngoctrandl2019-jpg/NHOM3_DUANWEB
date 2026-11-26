<?php
$orders = $orders ?? null;

if (!is_array($orders)) {
    http_response_code(500);
    echo '<p>Chưa tải được lịch sử đơn hàng.</p>';
    return;
}
$e = static fn($value): string =>
    htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>

<main class="mx-auto max-w-4xl p-6">
    <h1 class="mb-4 text-2xl font-bold">Lịch sử đơn hàng</h1>

    <?php if ($orders === []): ?>
        <p>Bạn chưa có đơn hàng nào.</p>
    <?php endif; ?>

    <?php foreach ($orders as $order): ?>
        <article class="mb-3 rounded border p-4">
            <p>Trạng thái: <?= $e($order['status']) ?></p>
            <p>Tổng tiền: <?= number_format((int) $order['total'], 0, ',', '.') ?> ₫</p>
            <a class="text-blue-700 underline" href="/orders/<?= rawurlencode($order['public_id']) ?>">
                Xem đơn <?= $e($order['public_id']) ?>
            </a>
        </article>
    <?php endforeach; ?>
</main>