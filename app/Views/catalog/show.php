<?php
/** @var array $product */
/** @var \Closure $e */
/** @var \Closure $money */
/** @var string $csrf */

$stock = (int) ($product['stock'] ?? 0);
?>

<p><a href="/products#catalog">← Về danh mục sản phẩm</a></p>

<article class="panel detail">
    <?php if (!empty($product['image_url'])): ?>
        <img src="<?= $e($product['image_url']) ?>" alt="<?= $e($product['name']) ?>" width="560" height="360">
    <?php endif; ?>

    <div class="stack">
        <h1><?= $e($product['name']) ?></h1>
        <p>Mã sản phẩm: <?= $e($product['sku'] ?? '') ?></p>
        <strong class="price"><?= $money((string) $product['price']) ?></strong>
        <p><?= nl2br($e($product['description'] ?? '')) ?></p>

        <?php if ($stock < 1): ?>
            <p>Sản phẩm đang hết hàng.</p>
        <?php elseif (\App\Support\Auth::userId() === null): ?>
            <a class="button" href="/login">Đăng nhập để thêm vào giỏ</a>
        <?php else: ?>
            <form method="post" action="/cart/add">
                <?php require $csrf; ?>
                <input type="hidden" name="product_id" value="<?= $e($product['id']) ?>">
                <label>
                    Số lượng
                    <input type="number" name="quantity" min="1" max="<?= $e(min(1000000, $stock)) ?>" value="1" required>
                </label>
                <button class="button" type="submit">Thêm vào giỏ</button>
            </form>
        <?php endif; ?>
    </div>
</article>