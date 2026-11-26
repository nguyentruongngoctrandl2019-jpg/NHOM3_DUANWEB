<?php

use app\Models\Order;

if (
    !isset($orders, $e, $money, $csrf)
    || !is_array($orders)
    || !is_callable($e)
    || !is_callable($money)
    || !is_string($csrf)
    || !is_file($csrf)
) {
    http_response_code(500);
    echo '<p>Chưa tải được dữ liệu quản lý đơn hàng.</p>';
    return;
}
?>

<h1>Quản lý đơn hàng</h1>

<div class="panel table-scroll">
    <?php if ($orders === []): ?>
        <p>Chưa có đơn hàng.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Người nhận</th>
                    <th>Tổng COD</th>
                    <th>Trạng thái</th>
                    <th>Cập nhật</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($orders as $order): ?>
                    <?php
                    $status = $order['status'];
                    $options = Order::TRANSITIONS[$status] ?? [];
                    $statusLabel = Order::LABELS[$status] ?? $status;
                    $publicId = (string) $order['public_id'];
                    ?>

                    <tr>
                        <td>
                            <?= $e($publicId) ?><br>
                            <span class="subtle"><?= $e($order['created_at']) ?></span>
                        </td>

                        <td><?= $e($order['recipient_name']) ?></td>
                        <td><?= $money((string) $order['grand_total']) ?></td>
                        <td><?= $e($statusLabel) ?></td>

                        <td>
                            <?php if ($options !== []): ?>
                                <form action="/admin/orders/<?= rawurlencode($publicId) ?>/status" method="post"
                                    class="inline-form">
                                    <?php require $csrf; ?>

                                    <input type="hidden" name="old_status" value="<?= $e($status) ?>">

                                    <select name="status" required aria-label="Trạng thái mới cho đơn <?= $e($publicId) ?>">
                                        <option value="">Chọn trạng thái</option>

                                        <?php foreach ($options as $option): ?>
                                            <option value="<?= $e($option) ?>">
                                                <?= $e(Order::LABELS[$option] ?? $option) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <button type="submit" class="button secondary">
                                        Lưu
                                    </button>
                                </form>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>