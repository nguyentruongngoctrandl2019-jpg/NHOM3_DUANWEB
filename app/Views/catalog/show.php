<?php

use App\Support\View;

$product = $product ?? null;
$relatedProducts = $relatedProducts ?? [];
$recommendedProducts = $recommendedProducts ?? [];

if (!$product) {
    echo '<div class="p-10 text-center">
            Không tìm thấy sản phẩm.
          </div>';
    return;
}

$price = (int) $product['price'];

$oldPrice = (int) (
    $product['old_price'] ?? $price
);

$discount = $oldPrice > $price
    ? round(($oldPrice - $price) / $oldPrice * 100)
    : 0;

?>

<div class="bg-gray-100 min-h-screen">

    <div class="max-w-[1600px] mx-auto px-4 md:px-8 py-6">

        <!-- ĐƯỜNG DẪN -->
        <div class="flex flex-wrap items-center gap-2
                    text-sm text-gray-500 mb-6">

            <a href="/" class="hover:text-red-600">
                Trang chủ
            </a>

            <span>/</span>

            <a href="/products" class="hover:text-red-600">
                Sản phẩm
            </a>

            <span>/</span>

            <span class="text-red-600 font-semibold">
                <?= View::e($product['name']) ?>
            </span>

        </div>

        <!-- CHI TIẾT SẢN PHẨM -->
        <div class="bg-white rounded-2xl shadow-sm
                    p-5 md:p-8 mb-8">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

                <!-- HÌNH ẢNH BÊN TRÁI -->
                <div>

                    <div class="relative border border-gray-200
                                rounded-2xl p-6 bg-gray-50">

                        <?php if ($discount > 0): ?>

                            <span class="absolute top-4 left-4
                                         bg-red-600 text-white
                                         px-4 py-2 rounded-full
                                         font-bold">

                                Giảm <?= $discount ?>%

                            </span>

                        <?php endif; ?>

                        <?php if (!empty($product['image'])): ?>

                            <img
                                src="<?= View::e($product['image']) ?>"
                                alt="<?= View::e($product['name']) ?>"
                                class="w-full h-80 md:h-[450px]
                                       object-contain">

                        <?php else: ?>

                            <div class="h-80 md:h-[450px]
                                        flex flex-col items-center
                                        justify-center text-gray-400">

                                <svg class="w-32 h-32"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">

                                    <rect x="3" y="3"
                                          width="18" height="18"
                                          rx="2" stroke-width="1"/>

                                    <circle cx="8" cy="8"
                                            r="1.5"/>

                                    <path d="M21 15l-5-5L5 21"
                                          stroke-width="1"/>

                                </svg>

                                <p class="mt-4">
                                    Hình ảnh sản phẩm
                                </p>

                            </div>

                        <?php endif; ?>

                    </div>

                    <!-- CAM KẾT -->
                    <div class="grid grid-cols-2 gap-4 mt-5">

                        <div class="border rounded-xl p-4
                                    text-center">

                            <svg class="w-8 h-8 mx-auto
                                        text-red-600 mb-2"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-width="1.5"
                                      d="M12 2l9 4v6c0 5-9 10-9 10S3 17 3 12V6z"/>

                                <path stroke-width="2"
                                      d="M8 12l3 3 5-6"/>

                            </svg>

                            <p class="font-semibold text-sm">
                                Hàng chính hãng
                            </p>

                        </div>

                        <div class="border rounded-xl p-4
                                    text-center">

                            <svg class="w-8 h-8 mx-auto
                                        text-red-600 mb-2"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-width="1.5"
                                      d="M2 4h13v13H2zM15 9h4l3 4v4h-7"/>

                                <circle cx="6" cy="18" r="2"/>
                                <circle cx="18" cy="18" r="2"/>

                            </svg>

                            <p class="font-semibold text-sm">
                                Giao hàng toàn quốc
                            </p>

                        </div>

                    </div>

                </div>

                <!-- THÔNG TIN BÊN PHẢI -->
                <div>

                    <span class="inline-block bg-green-100
                                 text-green-700 px-3 py-1
                                 rounded-full text-sm mb-4">

                        Sản phẩm chính hãng

                    </span>

                    <h1 class="text-3xl md:text-4xl
                               font-bold text-gray-900 mb-4">

                        <?= View::e($product['name']) ?>

                    </h1>

                    <!-- LƯỢT BÁN -->
                    <div class="flex flex-wrap items-center
                                gap-4 text-sm mb-6">

                        <span class="text-gray-500">
                            Đã bán
                            <?= (int) ($product['sold'] ?? 0) ?>
                            sản phẩm
                        </span>

                        <span class="text-gray-300">|</span>

                        <span class="text-gray-500">
                            Mã SP: CT<?= (int) $product['id'] ?>
                        </span>

                    </div>

                    <!-- GIÁ SẢN PHẨM -->
                    <div class="bg-red-50 border border-red-100
                                rounded-xl p-6 mb-6">

                        <p class="text-gray-500 text-sm mb-2">
                            Giá bán
                        </p>

                        <div class="flex flex-wrap
                                    items-center gap-4">

                            <span class="text-3xl md:text-4xl
                                         font-bold text-red-600">

                                <?= View::money($price) ?>

                            </span>

                            <?php if ($oldPrice > $price): ?>

                                <span class="line-through
                                             text-gray-400 text-lg">

                                    <?= View::money($oldPrice) ?>

                                </span>

                            <?php endif; ?>

                        </div>

                        <?php if ($oldPrice > $price): ?>

                            <p class="text-red-600 mt-3 text-sm">

                                Tiết kiệm
                                <?= View::money($oldPrice - $price) ?>

                            </p>

                        <?php endif; ?>

                    </div>

                    <!-- MÔ TẢ NGẮN -->
                    <p class="text-gray-600 leading-relaxed mb-6">

                        <?= View::e($product['description'] ?? '') ?>

                    </p>

                    <!-- ƯU ĐÃI -->
                    <div class="border border-red-200
                                rounded-xl overflow-hidden mb-6">

                        <div class="bg-red-600 text-white
                                    px-5 py-3 font-bold">

                            Ưu đãi khi mua hàng

                        </div>

                        <div class="p-5 space-y-3 text-gray-700 text-sm">

                            <p>✓ Cam kết sản phẩm chính hãng.</p>

                            <p>✓ Hỗ trợ tư vấn sản phẩm.</p>

                            <p>✓ Hỗ trợ bảo hành theo chính sách.</p>

                            <p>✓ Giao hàng trên toàn quốc.</p>

                        </div>

                    </div>

                    <!-- FORM THÊM GIỎ HÀNG -->
                    <form action="/cart/add" method="POST">

                        <input type="hidden"
                               name="product_id"
                               value="<?= (int) $product['id'] ?>">

                        <label class="block font-bold mb-3">
                            Số lượng
                        </label>

                        <div class="inline-flex items-center
                                    border border-gray-300
                                    rounded-lg overflow-hidden mb-6">

                            <button type="button"
                                    onclick="changeQuantity(-1)"
                                    class="w-12 h-12 bg-gray-100
                                           hover:bg-gray-200 text-xl">

                                −

                            </button>

                            <input type="number"
                                   id="quantity"
                                   name="quantity"
                                   value="1"
                                   min="1"
                                   max="10"
                                   required
                                   class="w-16 h-12 text-center
                                          outline-none">

                            <button type="button"
                                    onclick="changeQuantity(1)"
                                    class="w-12 h-12 bg-gray-100
                                           hover:bg-gray-200 text-xl">

                                +

                            </button>

                        </div>

                        <button type="submit"
                                class="w-full bg-red-600
                                       hover:bg-red-700
                                       text-white rounded-xl
                                       py-4 font-bold text-lg
                                       flex items-center
                                       justify-center gap-3">

                            <svg class="w-6 h-6"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-width="2"
                                      stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M3 3h2l2 12h12l2-9H6M9 21h.01M18 21h.01"/>

                            </svg>

                            THÊM VÀO GIỎ HÀNG

                        </button>

                    </form>

                    <a href="/products"
                       class="block text-center mt-3
                              border border-red-600
                              text-red-600 rounded-xl
                              py-4 font-semibold hover:bg-red-50">

                        Tiếp tục mua sắm

                    </a>

                </div>

            </div>

        </div>

        <!-- MÔ TẢ SẢN PHẨM -->
        <div class="bg-white rounded-2xl shadow-sm
                    p-6 md:p-8 mb-8">

            <h2 class="text-2xl font-bold text-gray-900
                       border-b pb-4 mb-5">

                Thông tin sản phẩm

            </h2>

            <h3 class="font-bold text-lg mb-4">

                <?= View::e($product['name']) ?>

            </h3>

            <p class="text-gray-600 leading-loose">

                <?= View::e($product['description'] ?? '') ?>

            </p>

            <p class="text-gray-600 leading-loose mt-4">

                Khám phá các sản phẩm công nghệ tại CT TECH STORE.
                Khách hàng có thể xem thông tin, lựa chọn sản phẩm
                và đặt hàng trực tuyến dễ dàng.

            </p>

        </div>

        <!-- THÔNG TIN MUA HÀNG -->
        <div class="bg-white rounded-2xl shadow-sm
                    p-6 md:p-8 mb-8">

            <h2 class="text-2xl font-bold mb-5">
                Thông tin mua hàng
            </h2>

            <div class="border rounded-xl overflow-hidden">

                <div class="grid grid-cols-2 bg-gray-100 p-4">

                    <span class="font-semibold">
                        Tên sản phẩm
                    </span>

                    <span>
                        <?= View::e($product['name']) ?>
                    </span>

                </div>

                <div class="grid grid-cols-2 p-4">

                    <span class="font-semibold">
                        Mã sản phẩm
                    </span>

                    <span>
                        CT<?= (int) $product['id'] ?>
                    </span>

                </div>

                <div class="grid grid-cols-2 bg-gray-100 p-4">

                    <span class="font-semibold">
                        Bảo hành
                    </span>

                    <span>
                        Theo chính sách sản phẩm
                    </span>

                </div>

                <div class="grid grid-cols-2 p-4">

                    <span class="font-semibold">
                        Hình thức mua hàng
                    </span>

                    <span>
                        Đặt hàng trực tuyến
                    </span>

                </div>

                <div class="grid grid-cols-2 bg-gray-100 p-4">

                    <span class="font-semibold">
                        Giao hàng
                    </span>

                    <span>
                        Toàn quốc
                    </span>

                </div>

            </div>

        </div>

        <!-- CÁC NHÓM SẢN PHẨM ĐỀ XUẤT -->
        <?php

        $sections = [
            [
                'title' => 'Sản phẩm liên quan',
                'subtitle' => 'Khám phá thêm sản phẩm cùng danh mục',
                'items' => $relatedProducts
            ],
            [
                'title' => 'Có thể bạn sẽ thích',
                'subtitle' => 'Những sản phẩm nổi bật dành cho bạn',
                'items' => $recommendedProducts
            ]
        ];

        ?>

        <?php foreach ($sections as $section): ?>

            <?php if (!empty($section['items'])): ?>

                <section class="bg-white rounded-2xl
                                shadow-sm p-5 md:p-8 mb-8">

                    <div class="flex flex-wrap items-center
                                justify-between gap-4 mb-6">

                        <div>

                            <h2 class="text-2xl md:text-3xl
                                       font-bold text-gray-900">

                                <?= View::e($section['title']) ?>

                            </h2>

                            <p class="text-gray-500 mt-2 text-sm">

                                <?= View::e($section['subtitle']) ?>

                            </p>

                        </div>

                        <a href="/products"
                           class="text-red-600 font-semibold
                                  hover:underline">

                            Xem tất cả →

                        </a>

                    </div>

                    <div class="grid grid-cols-2
                                md:grid-cols-3
                                xl:grid-cols-4 gap-5">

                        <?php foreach ($section['items'] as $item): ?>

                            <article class="relative border
                                            border-gray-200
                                            rounded-xl overflow-hidden
                                            hover:shadow-lg
                                            hover:border-red-300
                                            transition flex flex-col">

                                <a href="/products/<?= (int) $item['id'] ?>"
                                   class="block p-4">

                                    <?php if (!empty($item['image'])): ?>

                                        <img
                                            src="<?= View::e($item['image']) ?>"
                                            alt="<?= View::e($item['name']) ?>"
                                            class="w-full h-44 md:h-52
                                                   object-contain">

                                    <?php else: ?>

                                        <div class="h-44 md:h-52
                                                    bg-gray-50 rounded-lg
                                                    flex items-center
                                                    justify-center">

                                            <svg class="w-16 h-16
                                                        text-gray-300"
                                                 fill="none"
                                                 stroke="currentColor"
                                                 viewBox="0 0 24 24">

                                                <rect x="3" y="3"
                                                      width="18"
                                                      height="18"
                                                      rx="2"/>

                                                <circle cx="8" cy="8"
                                                        r="1.5"/>

                                                <path d="M21 15l-5-5L5 21"/>

                                            </svg>

                                        </div>

                                    <?php endif; ?>

                                </a>

                                <div class="p-4 pt-0
                                            flex flex-col flex-1">

                                    <a href="/products/<?= (int) $item['id'] ?>"
                                       class="font-bold text-gray-800
                                              hover:text-red-600
                                              min-h-[48px]">

                                        <?= View::e($item['name']) ?>

                                    </a>

                                    <p class="text-red-600
                                              font-bold text-lg mt-2">

                                        <?= View::money($item['price']) ?>

                                    </p>

                                    <?php if (
                                        ($item['old_price'] ?? 0)
                                        > $item['price']
                                    ): ?>

                                        <p class="text-gray-400
                                                  line-through text-sm">

                                            <?= View::money($item['old_price']) ?>

                                        </p>

                                    <?php endif; ?>

                                    <div class="mt-auto pt-4">

                                        <a href="/products/<?= (int) $item['id'] ?>"
                                           class="block text-center
                                                  border border-red-600
                                                  text-red-600
                                                  rounded-lg py-2 mb-2
                                                  hover:bg-red-50">

                                            Xem chi tiết

                                        </a>

                                        <form action="/cart/add"
                                              method="POST">

                                            <input type="hidden"
                                                   name="product_id"
                                                   value="<?= (int) $item['id'] ?>">

                                            <input type="hidden"
                                                   name="quantity"
                                                   value="1">

                                            <button type="submit"
                                                    class="w-full
                                                           bg-red-600
                                                           text-white
                                                           rounded-lg py-3
                                                           hover:bg-red-700">

                                                Thêm vào giỏ

                                            </button>

                                        </form>

                                    </div>

                                </div>

                            </article>

                        <?php endforeach; ?>

                    </div>

                </section>

            <?php endif; ?>

        <?php endforeach; ?>

    </div>

</div>

<script>

    function changeQuantity(amount) {

        const input = document.getElementById('quantity');

        let quantity = parseInt(input.value) || 1;

        quantity = quantity + amount;

        if (quantity < 1) {
            quantity = 1;
        }

        if (quantity > 10) {
            quantity = 10;
        }

        input.value = quantity;
    }

</script>
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

<?php
use App\Support\View;

// Nhận thông tin sản phẩm từ Controller
$product = $product ?? null;
?>

<section class="container-custom py-10">

    <?php if (!$product): ?>

        <div class="bg-white rounded-xl p-10 text-center">
            <h1 class="text-2xl font-bold mb-4">
                Không tìm thấy sản phẩm
            </h1>

            <a href="/products" class="btn-primary">
                Quay lại sản phẩm
            </a>
        </div>

    <?php else: ?>

        <?php
        $id = (int) ($product['id'] ?? 0);
        $name = (string) ($product['name'] ?? '');
        $price = $product['price'] ?? 0;
        $description = (string) (
            $product['description'] ?? ''
        );

        $image = (string) ($product['image'] ?? '');
        $imageUrl = '/assets/images/placeholder.svg';

        if (
            str_starts_with($image, '/assets/images/')
            && !str_contains($image, '..')
            && !str_contains($image, '\\')
        ) {
            $imageUrl = $image;
        }
        ?>

        <!-- Đường dẫn điều hướng -->
        <div class="mb-6 text-sm text-gray-600">
            <a href="/" class="hover:text-red-600">
                Trang chủ
            </a>
            /
            <a href="/products" class="hover:text-red-600">
                Sản phẩm
            </a>
            /
            <?= View::e($name) ?>
        </div>

        <!-- Chi tiết sản phẩm -->
        <div class="bg-white rounded-2xl shadow-sm
                    grid grid-cols-1 lg:grid-cols-2 gap-10 p-6">

            <!-- Hình ảnh -->
            <div class="flex items-center justify-center
                        bg-gray-50 rounded-xl p-6">

                <img
                    src="<?= View::e($imageUrl) ?>"
                    alt="<?= View::e($name) ?>"
                    class="w-full max-h-96 object-contain"
                >

            </div>

            <!-- Thông tin -->
            <div>

                <h1 class="text-3xl font-bold mb-5">
                    <?= View::e($name) ?>
                </h1>

                <p class="text-3xl font-bold
                          text-[#EF233C] mb-6">
                    <?= View::money($price) ?>
                </p>

                <div class="border-t border-gray-200
                            pt-5 mb-6">

                    <h2 class="text-xl font-semibold mb-3">
                        Mô tả sản phẩm
                    </h2>

                    <p class="text-gray-600 leading-7
                              whitespace-pre-line"><?= View::e($description) ?></p>

                </div>

                <!-- Form thêm vào giỏ hàng -->
                <form action="/cart/add" method="POST">

                    <input
                        type="hidden"
                        name="product_id"
                        value="<?= $id ?>"
                    >

                    <label for="quantity"
                           class="block font-medium mb-2">
                        Số lượng
                    </label>

                    <input
                        type="number"
                        id="quantity"
                        name="quantity"
                        value="1"
                        min="1"
                        max="10"
                        required
                        class="border border-gray-300
                               rounded-lg px-4 py-3 w-28 mb-5"
                    >

                    <button type="submit"
                            class="btn-primary w-full">
                        Thêm vào giỏ hàng
                    </button>

                </form>

                <div class="mt-6 p-4 bg-[#F9FCE7]
                            rounded-lg text-sm text-gray-700">
                    Sản phẩm chính hãng.
                    Hỗ trợ bảo hành và giao hàng toàn quốc.
                </div>

            </div>
        </div>

    <?php endif; ?>

</section>
