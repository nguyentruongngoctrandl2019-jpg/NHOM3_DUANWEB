<?php

use app\Models\Order;

if (
    !isset($order, $items, $phone, $address, $e, $money)
    || !is_array($order)
    || !is_array($items)
    || !is_string($phone)
    || !is_string($address)
    || !is_callable($e)
    || !is_callable($money)
) {
    echo '<p class="panel">Chưa tải được chi tiết đơn hàng.</p>';
    return;
}

$status = (string) $order['status'];
$statusLabel = Order::LABELS[$status] ?? $status;
?>

<div class="section-head">
    <h1>Chi tiết đơn hàng</h1>
    <a class="text-link" href="/orders">Lịch sử đơn hàng</a>
</div>

<div class="checkout-grid">
    <section class="panel stack">
        <h2>Sản phẩm đã đặt</h2>

        <?php foreach ($items as $item): ?>
            <div class="row-item">
                <strong>
                    <?= $e($item['product_name']) ?> × <?= $e($item['quantity']) ?>
                </strong>
                <span><?= $money((string) $item['unit_price']) ?> / sp</span>
            </div>
        <?php endforeach; ?>

        <div class="split">
            <span>Phí giao hàng</span>
            <span><?= $money((string) $order['shipping_fee']) ?></span>
        </div>

        <div class="split">
            <strong>Tổng tiền COD</strong>
            <strong class="price"><?= $money((string) $order['grand_total']) ?></strong>
        </div>
    </section>

    <aside class="panel stack">
        <h2>Thông tin đơn hàng</h2>
        <span class="badge"><?= $e($statusLabel) ?></span>
        <p class="subtle order-id"><?= $e($order['public_id']) ?></p>
        <p><?= $e($order['recipient_name']) ?> · <?= $e($phone) ?></p>
        <p><?= $e($address) ?></p>
    </aside>
</div>