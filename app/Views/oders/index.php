<?php

use App\Models\Order;

if (
    !isset($orders, $e, $money)
    || !is_array($orders)
    || !is_callable($e)
    || !is_callable($money)
) {
    echo '<p class="panel">Chưa tải được danh sách đơn hàng.</p>';
    return;
}
?>

<h1>Đơn hàng của tôi</h1>

<?php if ($orders === []): ?>
    <p class="panel">Bạn chưa có đơn hàng.</p>
<?php else: ?>
    <div class="panel stack">
        <?php foreach ($orders as $order): ?>
            <?php
            $publicId = (string) $order['public_id'];
            $status = (string) $order['status'];
            $statusLabel = Order::LABELS[$status] ?? $status;
            ?>

            <article class="row-item">
                <div>
                    <a class="text-link" href="/orders/<?= rawurlencode($publicId) ?>">
                        <?= $e($publicId) ?>
                    </a>
                    <p class="subtle"><?= $e($order['created_at']) ?></p>
                </div>

                <div class="actions">
                    <span class="badge"><?= $e($statusLabel) ?></span>
                    <strong><?= $money((string) $order['grand_total']) ?></strong>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>