<?php

use App\Support\View;
use App\Support\Auth;

// Nhan du lieu tu Controller.
$product = $product ?? null;
$relatedProducts = $relatedProducts ?? [];
$recommendedProducts = $recommendedProducts ?? [];

// Kiem tra san pham.
if (!$product) {
    echo '
        <div class="p-10 text-center">
            <h2 class="text-2xl font-bold mb-4">
                Không tìm thấy sản phẩm
            </h2>
            <a href="/products"
               class="text-red-600">
                Quay lại danh sách sản phẩm
            </a>
        </div>
    ';
    return;
}

// Ham ho tro hien thi du lieu.
$escape = $e ?? static function ($value): string {
    return View::e((string) $value);
};

$formatMoney = $money ?? static function ($value): string {
    return View::money((string) $value);
};

// Thong tin san pham.
$id = (int) ($product['id'] ?? 0);

$name = (string) ($product['name'] ?? '');

$price = $product['price'] ?? 0;

$oldPrice = $product['old_price'] ?? $price;

$description = (string) (
    $product['description'] ?? ''
);

$stock = max(0, (int) ($product['stock'] ?? 0));

$sold = (int) ($product['sold'] ?? 0);

$sku = $product['sku'] ?? ('CT' . $id);

$image = $product['image_url']
    ?? $product['image']
    ?? '';

// Tinh phan tram giam gia.
$discount = (
    (float) $oldPrice > (float) $price
    && (float) $oldPrice > 0
)
    ? (int) round(
        ((float) $oldPrice - (float) $price)
        / (float) $oldPrice * 100
    )
    : 0;

// Kiem tra dang nhap.
$isLoggedIn = Auth::userId() !== null;

// Gioi han so luong.
$maxQuantity = min(10, $stock);

// Danh sach san pham de xuat.
$sections = [
    [
        'title' => 'Sản phẩm liên quan',
        'subtitle' => 'Khám phá sản phẩm cùng danh mục',
        'items' => $relatedProducts,
    ],
    [
        'title' => 'Có thể bạn sẽ thích',
        'subtitle' => 'Những sản phẩm nổi bật dành cho bạn',
        'items' => $recommendedProducts,
    ],
];

?>

<div class="bg-gray-100 min-h-screen">

    <div class="max-w-[1600px] mx-auto px-4 md:px-8 py-6">

        <!-- DUONG DAN DIEU HUONG -->

        <div class="flex flex-wrap items-center gap-2
                    text-sm text-gray-500 mb-6">

            <a href="/" class="hover:text-red-600">
                Trang chủ
            </a>

            <span>/</span>

            <a href="/products"
               class="hover:text-red-600">
                Sản phẩm
            </a>

            <span>/</span>

            <span class="text-red-600 font-semibold">
                <?= $escape($name) ?>
            </span>

        </div>

        <!-- CHI TIET SAN PHAM -->

        <div class="bg-white rounded-2xl shadow-sm
                    p-5 md:p-8 mb-8">

            <div class="grid grid-cols-1
                        lg:grid-cols-2 gap-10">

                <!-- HINH ANH SAN PHAM -->

                <div>

                    <div class="relative border
                                border-gray-200 rounded-2xl
                                p-6 bg-gray-50">

                        <?php if ($discount > 0): ?>

                            <span class="absolute top-4 left-4
                                         bg-red-600 text-white
                                         px-4 py-2 rounded-full
                                         font-bold">

                                Giảm <?= $discount ?>%

                            </span>

                        <?php endif; ?>

                        <?php if (!empty($image)): ?>

                            <img
                                src="<?= $escape($image) ?>"
                                alt="<?= $escape($name) ?>"
                                class="w-full h-80 md:h-[450px]
                                       object-contain"
                            >

                        <?php else: ?>

                            <div class="h-80 md:h-[450px]
                                        flex flex-col items-center
                                        justify-center text-gray-400">

                                <svg class="w-32 h-32"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">

                                    <rect x="3" y="3"
                                          width="18"
                                          height="18"
                                          rx="2"/>

                                    <circle cx="8"
                                            cy="8"
                                            r="1.5"/>

                                    <path d="M21 15l-5-5L5 21"/>

                                </svg>

                                <p class="mt-4">
                                    Chưa có hình ảnh sản phẩm
                                </p>

                            </div>

                        <?php endif; ?>

                    </div>

                    <!-- CAM KET CUA HANG -->

                    <div class="grid grid-cols-2 gap-4 mt-5">

                        <div class="border rounded-xl
                                    p-4 text-center">

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

                        <div class="border rounded-xl
                                    p-4 text-center">

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

                <!-- THONG TIN SAN PHAM -->

                <div>

                    <span class="inline-block bg-green-100
                                 text-green-700 px-3 py-1
                                 rounded-full text-sm mb-4">

                        Sản phẩm chính hãng

                    </span>

                    <h1 class="text-3xl md:text-4xl
                               font-bold text-gray-900 mb-4">

                        <?= $escape($name) ?>

                    </h1>

                    <!-- MA SAN PHAM VA LUOT BAN -->

                    <div class="flex flex-wrap items-center
                                gap-4 text-sm mb-6">

                        <span class="text-gray-500">

                            Mã SP: <?= $escape($sku) ?>

                        </span>

                        <span class="text-gray-300">|</span>

                        <span class="text-gray-500">

                            Đã bán <?= $sold ?> sản phẩm

                        </span>

                        <span class="text-gray-300">|</span>

                        <?php if ($stock > 0): ?>

                            <span class="text-green-600
                                         font-semibold">

                                Còn <?= $stock ?> sản phẩm

                            </span>

                        <?php else: ?>

                            <span class="text-red-600
                                         font-semibold">

                                Hết hàng

                            </span>

                        <?php endif; ?>

                    </div>

                    <!-- GIA SAN PHAM -->

                    <div class="bg-red-50 border
                                border-red-100 rounded-xl
                                p-6 mb-6">

                        <p class="text-gray-500
                                  text-sm mb-2">

                            Giá bán

                        </p>

                        <div class="flex flex-wrap
                                    items-center gap-4">

                            <span class="text-3xl md:text-4xl
                                         font-bold text-red-600">

                                <?= $formatMoney($price) ?>

                            </span>

                            <?php if ($discount > 0): ?>

                                <span class="line-through
                                             text-gray-400
                                             text-lg">

                                    <?= $formatMoney($oldPrice) ?>

                                </span>

                            <?php endif; ?>

                        </div>

                        <?php if ($discount > 0): ?>

                            <p class="text-red-600
                                      mt-3 text-sm">

                                Tiết kiệm

                                <?= $formatMoney(
                                    (float) $oldPrice
                                    - (float) $price
                                ) ?>

                            </p>

                        <?php endif; ?>

                    </div>

                    <!-- MO TA NGAN -->

                    <p class="text-gray-600 leading-relaxed
                              whitespace-pre-line mb-6"><?= $escape($description) ?></p>

                    <!-- UU DAI -->

                    <div class="border border-red-200
                                rounded-xl overflow-hidden mb-6">

                        <div class="bg-red-600 text-white
                                    px-5 py-3 font-bold">

                            Ưu đãi khi mua hàng

                        </div>

                        <div class="p-5 space-y-3
                                    text-gray-700 text-sm">

                            <p>
                                ✓ Cam kết sản phẩm chính hãng.
                            </p>

                            <p>
                                ✓ Hỗ trợ tư vấn sản phẩm.
                            </p>

                            <p>
                                ✓ Hỗ trợ bảo hành theo chính sách.
                            </p>

                            <p>
                                ✓ Giao hàng trên toàn quốc.
                            </p>

                        </div>

                    </div>

                    <!-- KIEM TRA TON KHO VA DANG NHAP -->

                    <?php if ($stock < 1): ?>

                        <div class="bg-gray-100 rounded-xl
                                    p-4 text-center
                                    font-bold text-gray-500">

                            SẢN PHẨM ĐANG HẾT HÀNG

                        </div>

                    <?php elseif (!$isLoggedIn): ?>

                        <a href="/login"
                           class="block w-full text-center
                                  bg-red-600 hover:bg-red-700
                                  text-white rounded-xl
                                  py-4 font-bold">

                            ĐĂNG NHẬP ĐỂ MUA HÀNG

                        </a>

                    <?php elseif (empty($csrf) || !is_file($csrf)): ?>

                        <p class="text-red-600 text-sm">

                            Không thể khởi tạo biểu mẫu giỏ hàng.

                        </p>

                    <?php else: ?>

                        <!-- FORM THEM GIO HANG -->

                        <form action="/cart/add"
                              method="POST">

                            <?php require $csrf; ?>

                            <input
                                type="hidden"
                                name="product_id"
                                value="<?= $id ?>"
                            >

                            <label for="quantity"
                                   class="block font-bold mb-3">

                                Số lượng

                            </label>

                            <div class="inline-flex items-center
                                        border border-gray-300
                                        rounded-lg overflow-hidden
                                        mb-6">

                                <button
                                    type="button"
                                    onclick="changeQuantity(-1)"
                                    class="w-12 h-12 bg-gray-100
                                           hover:bg-gray-200
                                           text-xl">

                                    −

                                </button>

                                <input
                                    type="number"
                                    id="quantity"
                                    name="quantity"
                                    value="1"
                                    min="1"
                                    max="<?= $maxQuantity ?>"
                                    required
                                    class="w-16 h-12 text-center
                                           outline-none"
                                >

                                <button
                                    type="button"
                                    onclick="changeQuantity(1)"
                                    class="w-12 h-12 bg-gray-100
                                           hover:bg-gray-200
                                           text-xl">

                                    +

                                </button>

                            </div>

                            <button
                                type="submit"
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

                    <?php endif; ?>

                    <a href="/products"
                       class="block text-center mt-3
                              border border-red-600
                              text-red-600 rounded-xl
                              py-4 font-semibold
                              hover:bg-red-50">

                        Tiếp tục mua sắm

                    </a>

                </div>

            </div>

        </div>

        <!-- MO TA CHI TIET SAN PHAM -->

        <div class="bg-white rounded-2xl shadow-sm
                    p-6 md:p-8 mb-8">

            <h2 class="text-2xl font-bold text-gray-900
                       border-b pb-4 mb-5">

                Thông tin sản phẩm

            </h2>

            <h3 class="font-bold text-lg mb-4">

                <?= $escape($name) ?>

            </h3>

            <p class="text-gray-600 leading-loose
                      whitespace-pre-line"><?= $escape($description) ?></p>

            <p class="text-gray-600 leading-loose mt-4">

                Khám phá các sản phẩm công nghệ tại
                CT TECH STORE. Khách hàng có thể xem thông tin,
                lựa chọn sản phẩm và đặt hàng trực tuyến dễ dàng.

            </p>

        </div>

        <!-- THONG TIN MUA HANG -->

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
                        <?= $escape($name) ?>
                    </span>

                </div>

                <div class="grid grid-cols-2 p-4">

                    <span class="font-semibold">
                        Mã sản phẩm
                    </span>

                    <span>
                        <?= $escape($sku) ?>
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
                        Tình trạng
                    </span>

                    <span>

                        <?= $stock > 0
                            ? 'Còn hàng'
                            : 'Hết hàng' ?>

                    </span>

                </div>

                <div class="grid grid-cols-2 bg-gray-100 p-4">

                    <span class="font-semibold">
                        Hình thức mua hàng
                    </span>

                    <span>
                        Đặt hàng trực tuyến
                    </span>

                </div>

                <div class="grid grid-cols-2 p-4">

                    <span class="font-semibold">
                        Giao hàng
                    </span>

                    <span>
                        Toàn quốc
                    </span>

                </div>

            </div>

        </div>

        <!-- SAN PHAM LIEN QUAN VA DE XUAT -->

        <?php foreach ($sections as $section): ?>

            <?php if (empty($section['items'])) {
                continue;
            } ?>

            <section class="bg-white rounded-2xl
                            shadow-sm p-5 md:p-8 mb-8">

                <div class="flex flex-wrap items-center
                            justify-between gap-4 mb-6">

                    <div>

                        <h2 class="text-2xl md:text-3xl
                                   font-bold text-gray-900">

                            <?= $escape($section['title']) ?>

                        </h2>

                        <p class="text-gray-500 mt-2 text-sm">

                            <?= $escape($section['subtitle']) ?>

                        </p>

                    </div>

                    <a href="/products"
                       class="text-red-600 font-semibold
                              hover:underline">

                        Xem tất cả →

                    </a>

                </div>

                <div class="grid grid-cols-2
                            md:grid-cols-3 xl:grid-cols-4 gap-5">

                    <?php foreach ($section['items'] as $item): ?>

                        <?php

                        $itemId = (int) ($item['id'] ?? 0);

                        $itemName = $item['name'] ?? '';

                        $itemPrice = $item['price'] ?? 0;

                        $itemOldPrice =
                            $item['old_price'] ?? $itemPrice;

                        $itemImage = $item['image_url']
                            ?? $item['image']
                            ?? '';

                        $itemStock = max(
                            0,
                            (int) ($item['stock'] ?? 0)
                        );

                        ?>

                        <article class="relative border
                                        border-gray-200
                                        rounded-xl overflow-hidden
                                        hover:shadow-lg
                                        hover:border-red-300
                                        transition flex flex-col">

                            <a href="/products/<?= $itemId ?>"
                               class="block p-4">

                                <?php if (!empty($itemImage)): ?>

                                    <img
                                        src="<?= $escape($itemImage) ?>"
                                        alt="<?= $escape($itemName) ?>"
                                        loading="lazy"
                                        class="w-full h-44 md:h-52
                                               object-contain"
                                    >

                                <?php else: ?>

                                    <div class="h-44 md:h-52
                                                bg-gray-50
                                                rounded-lg flex
                                                items-center
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

                                            <circle cx="8"
                                                    cy="8"
                                                    r="1.5"/>

                                            <path d="M21 15l-5-5L5 21"/>

                                        </svg>

                                    </div>

                                <?php endif; ?>

                            </a>

                            <div class="p-4 pt-0
                                        flex flex-col flex-1">

                                <a href="/products/<?= $itemId ?>"
                                   class="font-bold text-gray-800
                                          hover:text-red-600
                                          min-h-[48px]">

                                    <?= $escape($itemName) ?>

                                </a>

                                <p class="text-red-600 font-bold
                                          text-lg mt-2">

                                    <?= $formatMoney($itemPrice) ?>

                                </p>

                                <?php if (
                                    (float) $itemOldPrice
                                    > (float) $itemPrice
                                ): ?>

                                    <p class="text-gray-400
                                              line-through text-sm">

                                        <?= $formatMoney($itemOldPrice) ?>

                                    </p>

                                <?php endif; ?>

                                <div class="mt-auto pt-4">

                                    <a href="/products/<?= $itemId ?>"
                                       class="block text-center
                                              border border-red-600
                                              text-red-600
                                              rounded-lg py-2 mb-2
                                              hover:bg-red-50">

                                        Xem chi tiết

                                    </a>

                                    <?php if ($itemStock < 1): ?>

                                        <p class="bg-gray-100
                                                  text-gray-500
                                                  text-center
                                                  rounded-lg py-3">

                                            Hết hàng

                                        </p>

                                    <?php elseif (!$isLoggedIn): ?>

                                        <a href="/login"
                                           class="block text-center
                                                  bg-red-600
                                                  text-white
                                                  rounded-lg py-3">

                                            Đăng nhập để mua

                                        </a>

                                    <?php elseif (
                                        !empty($csrf)
                                        && is_file($csrf)
                                    ): ?>

                                        <form action="/cart/add"
                                              method="POST">

                                            <?php require $csrf; ?>

                                            <input
                                                type="hidden"
                                                name="product_id"
                                                value="<?= $itemId ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="quantity"
                                                value="1"
                                            >

                                            <button
                                                type="submit"
                                                class="w-full
                                                       bg-red-600
                                                       text-white
                                                       rounded-lg
                                                       py-3
                                                       hover:bg-red-700">

                                                Thêm vào giỏ

                                            </button>

                                        </form>

                                    <?php endif; ?>

                                </div>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            </section>

        <?php endforeach; ?>

    </div>

</div>

<script>

    function changeQuantity(amount) {

        const input = document.getElementById('quantity');

        if (!input) {
            return;
        }

        const min = Number(input.min) || 1;

        const max = Number(input.max) || 10;

        let quantity = parseInt(input.value, 10) || min;

        quantity += amount;

        quantity = Math.max(
            min,
            Math.min(max, quantity)
        );

        input.value = quantity;
    }

</script>