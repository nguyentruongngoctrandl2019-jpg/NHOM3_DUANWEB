
<?php
use App\Support\View;

// Dữ liệu sản phẩm do Controller truyền vào
$products = $products ?? [];
$categories = $categories ?? [];
$search = $search ?? '';
?>

<section class="container-custom py-10">

    <!-- Tiêu đề trang -->
    <div class="mb-8">
        <h1 class="section-title">
            Sản phẩm điện máy
        </h1>

        <p class="text-gray-600 mt-2">
            Khám phá điện thoại, laptop và phụ kiện
            tại CT TECH STORE.
        </p>
    </div>

    <!-- Tìm kiếm sản phẩm -->
    <form action="/products" method="GET"
          class="bg-white rounded-xl shadow-sm p-5 mb-8">

        <div class="flex flex-col md:flex-row gap-4">

            <input
                type="search"
                name="search"
                value="<?= View::e($search) ?>"
                placeholder="Nhập tên sản phẩm..."
                class="flex-1 border border-gray-300
                       rounded-lg px-4 py-3"
            >

            <button type="submit" class="btn-primary">
                Tìm kiếm
            </button>

        </div>
    </form>

    <!-- Danh mục -->
    <div class="flex flex-wrap gap-3 mb-8">

        <a href="/products"
           class="btn-outline">
            Tất cả
        </a>

        <?php foreach ($categories as $category): ?>
            <a
                href="/products?category=<?= urlencode(
                    (string) ($category['slug'] ?? '')
                ) ?>"
                class="btn-outline"
            >
                <?= View::e($category['name'] ?? '') ?>
            </a>
        <?php endforeach; ?>

    </div>

    <!-- Danh sách sản phẩm -->
    <?php if (empty($products)): ?>

        <div class="bg-white rounded-xl p-10 text-center">
            <p class="text-gray-500">
                Hiện chưa có sản phẩm để hiển thị.
            </p>
        </div>

    <?php else: ?>

        <div class="grid grid-cols-1 sm:grid-cols-2
                    lg:grid-cols-4 gap-6">

            <?php foreach ($products as $product): ?>

                <?php
                $id = (int) ($product['id'] ?? 0);
                $name = (string) ($product['name'] ?? '');
                $price = $product['price'] ?? 0;
                $image = (string) (
                    $product['image'] ?? ''
                );

                // Chỉ cho phép ảnh từ thư mục assets
                $imageUrl = '/assets/images/placeholder.svg';

                if (
                    str_starts_with($image, '/assets/images/')
                    && !str_contains($image, '..')
                    && !str_contains($image, '\\')
                ) {
                    $imageUrl = $image;
                }
                ?>

                <article class="product-card">

                    <a href="/products/<?= $id ?>">

                        <img
                            src="<?= View::e($imageUrl) ?>"
                            alt="<?= View::e($name) ?>"
                            class="product-image"
                            loading="lazy"
                        >

                    </a>

                    <div class="p-5">

                        <h2 class="product-name mb-3">
                            <a href="/products/<?= $id ?>"
                               class="hover:text-red-600">
                                <?= View::e($name) ?>
                            </a>
                        </h2>

                        <p class="product-price mb-4">
                            <?= View::money($price) ?>
                        </p>

                        <a
                            href="/products/<?= $id ?>"
                            class="btn-primary w-full"
                        >
                            Xem chi tiết
                        </a>

                    </div>
                </article>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</section>
