<?php

$title = 'Danh mục điện máy';

$e = $e ?? throw new \LogicException('Thiếu hàm escape khi hiển thị danh mục.');
$money = $money ?? throw new \LogicException('Thiếu hàm định dạng giá.');
$products = $products ?? throw new \LogicException('Thiếu dữ liệu sản phẩm.');
$categories = $categories ?? throw new \LogicException('Thiếu dữ liệu danh mục.');
$filters = $filters ?? throw new \LogicException('Thiếu dữ liệu bộ lọc.');
$pagination = $pagination ?? throw new \LogicException('Thiếu dữ liệu phân trang.');

?>

<section class="hero">
    <div>
        <h1>Điện máy cho mọi căn nhà</h1>
        <p>Khám phá tivi, tủ lạnh, máy giặt, điều hòa và thiết bị gia dụng tại CT Tech Store.</p>
        <a class="button" href="#catalog">Xem sản phẩm</a>
    </div>
</section>

<section id="catalog">
    <div class="section-head">
        <h2>Danh mục sản phẩm</h2>
        <span><?= $e($pagination['total']) ?> sản phẩm</span>
    </div>

    <form action="/products#catalog" method="get" class="filter panel">
        <label>
            Tìm sản phẩm
            <input type="search" name="q" maxlength="100" placeholder="Nhập tên hoặc mã SKU"
                value="<?= $e($filters['q']) ?>">
        </label>

        <label>
            Danh mục
            <select name="category">
                <option value="">Tất cả danh mục</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $e($category['id']) ?>" <?= $filters['category'] === (int) $category['id'] ? 'selected' : '' ?>>
                        <?= $e($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit" class="button">Tìm kiếm</button>

        <?php if ($filters['q'] !== '' || $filters['category'] !== null): ?>
            <a class="button secondary" href="/products#catalog">Xóa bộ lọc</a>
        <?php endif; ?>
    </form>

    <?php if ($products === []): ?>
        <p class="panel">Không tìm thấy sản phẩm phù hợp.</p>
    <?php else: ?>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($products as $product): ?>
                <?php $productUrl = '/products/' . rawurlencode((string) $product['id']); ?>
                <article class="product-card">
                    <?php if (!empty($product['image_url'])): ?>
                        <a href="<?= $e($productUrl) ?>">
                            <img src="<?= $e($product['image_url']) ?>" alt="<?= $e($product['name']) ?>" loading="lazy" width="560"
                                height="360">
                        </a>
                    <?php endif; ?>

                    <div class="product-content">
                        <span class="eyebrow">
                            <?= $e($product['category_name']) ?> · <?= $e($product['sku']) ?>
                        </span>
                        <h3><a href="<?= $e($productUrl) ?>"><?= $e($product['name']) ?></a></h3>
                        <div class="split">
                            <strong class="price"><?= $money((string) $product['price']) ?></strong>
                            <span class="badge"><?= (int) $product['stock'] > 0 ? 'Còn hàng' : 'Hết hàng' ?></span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($pagination['pages'] > 1): ?>
        <nav aria-label="Phân trang" class="pagination">
            <?php if ($pagination['page'] > 1): ?>
                <a class="button secondary"
                    href="/products?<?= $e(http_build_query(array_replace($filters, ['page' => $pagination['page'] - 1]))) ?>#catalog">
                    Trang trước
                </a>
            <?php endif; ?>

            <span>Trang <?= $e($pagination['page']) ?> / <?= $e($pagination['pages']) ?></span>

            <?php if ($pagination['page'] < $pagination['pages']): ?>
                <a class="button secondary"
                    href="/products?<?= $e(http_build_query(array_replace($filters, ['page' => $pagination['page'] + 1]))) ?>#catalog">
                    Trang sau
                </a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
</section>