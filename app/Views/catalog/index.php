
<?php

use App\Support\View;

$products = $products ?? [];
$categories = $categories ?? [];
$search = $search ?? '';
$category = $category ?? '';

?>

<div class="w-full bg-gray-100 min-h-screen">

    <div class="max-w-[1600px] mx-auto px-4 md:px-8 py-6">

        <!-- Đường dẫn -->
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-5">
            <a href="/" class="hover:text-red-600">
                Trang chủ
            </a>

            <span>/</span>

            <span class="text-red-600 font-semibold">
                Sản phẩm
            </span>
        </div>

        <!-- Banner danh sách sản phẩm -->
        <div class="bg-gradient-to-r from-red-800 via-red-600 to-red-500
                    rounded-xl p-8 md:p-12 mb-8 text-white">

            <p class="uppercase tracking-widest text-sm mb-3">
                CT TECH STORE
            </p>

            <h1 class="text-2xl md:text-4xl font-bold mb-3">
                KHÁM PHÁ THẾ GIỚI CÔNG NGHỆ
            </h1>

            <p class="text-red-100">
                Sản phẩm chính hãng - Giá tốt mỗi ngày
            </p>

            <div class="flex flex-wrap gap-3 mt-5">
                <span class="bg-white/20 px-4 py-2 rounded-full text-sm">
                    Bảo hành chính hãng
                </span>

                <span class="bg-white/20 px-4 py-2 rounded-full text-sm">
                    Giao hàng toàn quốc
                </span>
            </div>

        </div>

        <!-- Bố cục danh sách -->
        <div class="flex flex-col lg:flex-row gap-6 items-start">

            <!-- SIDEBAR -->
            <aside class="w-full lg:w-64 shrink-0">

                <!-- Danh mục -->
                <div class="bg-white rounded-xl shadow-sm p-5 mb-5">

                    <h2 class="text-lg font-bold text-gray-800
                               border-b pb-4 mb-4">

                        Danh mục sản phẩm

                    </h2>

                    <a href="/products"
                       class="block px-4 py-3 rounded-lg mb-2
                       <?= $category === ''
                           ? 'bg-red-600 text-white'
                           : 'text-gray-700 hover:bg-red-50' ?>">

                        Tất cả sản phẩm

                    </a>

                    <?php foreach ($categories as $item): ?>

                        <a href="/products?category=<?= urlencode($item['slug']) ?>"
                           class="flex items-center justify-between
                                  px-4 py-3 rounded-lg mb-2
                           <?= $category === $item['slug']
                               ? 'bg-red-600 text-white'
                               : 'text-gray-700 hover:bg-red-50 hover:text-red-600' ?>">

                            <span>
                                <?= View::e($item['name']) ?>
                            </span>

                            <svg class="w-4 h-4"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-width="2"
                                      stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M9 18l6-6-6-6"/>

                            </svg>

                        </a>

                    <?php endforeach; ?>

                </div>

                <!-- Tìm kiếm -->
                <div class="bg-white rounded-xl shadow-sm p-5 mb-5">

                    <h2 class="font-bold text-lg mb-4">
                        Tìm kiếm sản phẩm
                    </h2>

                    <form action="/products" method="GET">

                        <input type="hidden"
                               name="category"
                               value="<?= View::e($category) ?>">

                        <input type="text"
                               name="search"
                               value="<?= View::e($search) ?>"
                               placeholder="Nhập tên sản phẩm..."
                               class="w-full border border-gray-300
                                      rounded-lg px-3 py-3 mb-3
                                      focus:outline-none focus:border-red-500">

                        <button type="submit"
                                class="w-full bg-red-600 text-white
                                       py-3 rounded-lg hover:bg-red-700">

                            Tìm kiếm

                        </button>

                    </form>

                </div>

                <!-- Quảng cáo -->
                <div class="bg-gray-900 text-white rounded-xl p-6">

                    <p class="text-red-400 font-bold text-sm mb-2">
                        ƯU ĐÃI ĐẶC BIỆT
                    </p>

                    <h3 class="text-2xl font-bold mb-3">
                        Công nghệ chính hãng
                    </h3>

                    <p class="text-gray-300 text-sm mb-5">
                        Mua sắm dễ dàng, giao hàng tận nơi,
                        bảo hành uy tín.
                    </p>

                    <a href="/products"
                       class="block text-center bg-red-600
                              rounded-lg py-3 font-semibold">

                        Khám phá ngay

                    </a>

                </div>

            </aside>

            <!-- DANH SÁCH SẢN PHẨM -->
            <section class="flex-1 min-w-0 w-full">

                <!-- Tiêu đề và sắp xếp -->
                <div class="bg-white rounded-xl shadow-sm
                            p-5 mb-5 flex flex-wrap
                            items-center justify-between gap-4">

                    <div>

                        <h2 class="text-2xl font-bold text-gray-800">
                            Danh sách sản phẩm
                        </h2>

                        <p class="text-gray-500 text-sm mt-1">
                            Tìm thấy <?= count($products) ?> sản phẩm
                        </p>

                    </div>

                    <select id="sortProducts"
                            class="border border-gray-300
                                   rounded-lg px-4 py-3 text-sm">

                        <option value="default">
                            Sắp xếp mặc định
                        </option>

                        <option value="price-asc">
                            Giá thấp đến cao
                        </option>

                        <option value="price-desc">
                            Giá cao đến thấp
                        </option>

                        <option value="best-seller">
                            Bán chạy nhất
                        </option>

                    </select>

                </div>

                <!-- Không tìm thấy sản phẩm -->
                <?php if (empty($products)): ?>

                    <div class="bg-white rounded-xl
                                text-center py-16 px-5">

                        <h3 class="text-xl font-bold text-gray-700">
                            Không tìm thấy sản phẩm
                        </h3>

                        <p class="text-gray-500 mt-3 mb-6">
                            Vui lòng thử tìm kiếm sản phẩm khác.
                        </p>

                        <a href="/products"
                           class="bg-red-600 text-white
                                  px-6 py-3 rounded-lg">

                            Xem tất cả sản phẩm

                        </a>

                    </div>

                <?php endif; ?>

                <!-- Lưới sản phẩm -->
                <div id="productList"
                     class="grid grid-cols-2 md:grid-cols-3
                            xl:grid-cols-4 gap-4">

                    <?php foreach ($products as $product): ?>

                        <?php

                        $price = (int) $product['price'];

                        $oldPrice = (int) (
                            $product['old_price'] ?? $price
                        );

                        $discount = $oldPrice > $price
                            ? round(
                                ($oldPrice - $price)
                                / $oldPrice * 100
                            )
                            : 0;

                        ?>

                        <!-- Thẻ sản phẩm -->
                        <article
                            data-price="<?= $price ?>"
                            data-sold="<?= (int) ($product['sold'] ?? 0) ?>"
                            class="relative bg-white rounded-xl
                                   border border-gray-200
                                   overflow-hidden flex flex-col
                                   hover:shadow-lg hover:border-red-300
                                   transition duration-300">

                            <!-- Nhãn giảm giá -->
                            <?php if ($discount > 0): ?>

                                <span class="absolute top-3 left-3 z-10
                                             bg-red-600 text-white
                                             text-xs font-bold
                                             px-3 py-1 rounded-full">

                                    -<?= $discount ?>%

                                </span>

                            <?php endif; ?>

                            <!-- Hình ảnh -->
                            <a href="/products/<?= (int) $product['id'] ?>"
                               class="block p-4">

                                <?php if (!empty($product['image'])): ?>

                                    <img
                                        src="<?= View::e($product['image']) ?>"
                                        alt="<?= View::e($product['name']) ?>"
                                        class="w-full h-40 md:h-52
                                               object-contain
                                               hover:scale-105 transition">

                                <?php else: ?>

                                    <div class="w-full h-40 md:h-52
                                                bg-gray-50 rounded-lg
                                                flex items-center
                                                justify-center">

                                        <svg class="w-16 h-16 text-gray-300"
                                             fill="none"
                                             stroke="currentColor"
                                             viewBox="0 0 24 24">

                                            <rect x="3" y="3"
                                                  width="18" height="18"
                                                  rx="2" stroke-width="1.5"/>

                                            <circle cx="8.5" cy="8.5"
                                                    r="1.5"/>

                                            <path d="M21 15l-5-5L5 21"
                                                  stroke-width="1.5"/>

                                        </svg>

                                    </div>

                                <?php endif; ?>

                            </a>

                            <!-- Thông tin sản phẩm -->
                            <div class="p-4 pt-0 flex flex-col flex-1">

                                <p class="text-xs text-green-600
                                          font-medium mb-2">

                                    Hàng chính hãng

                                </p>

                                <a href="/products/<?= (int) $product['id'] ?>"
                                   class="font-bold text-gray-800
                                          hover:text-red-600
                                          min-h-[48px]">

                                    <?= View::e($product['name']) ?>

                                </a>

                                <!-- Giá bán -->
                                <p class="text-red-600 font-bold
                                          text-base md:text-xl mt-2">

                                    <?= View::money($price) ?>

                                </p>

                                <!-- Giá cũ -->
                                <div class="min-h-[24px]">

                                    <?php if ($oldPrice > $price): ?>

                                        <p class="text-gray-400
                                                  line-through text-sm">

                                            <?= View::money($oldPrice) ?>

                                        </p>

                                    <?php endif; ?>

                                </div>

                                <!-- Lượt bán -->
                                <div class="flex items-center
                                            justify-between gap-1
                                            text-xs mb-4 mt-2">

                                    <span class="text-green-600">
                                        Chính hãng
                                    </span>

                                    <span class="text-gray-500">
                                        Đã bán
                                        <?= (int) ($product['sold'] ?? 0) ?>
                                    </span>

                                </div>

                                <!-- Nút thao tác -->
                                <div class="mt-auto space-y-2">

                                    <a href="/products/<?= (int) $product['id'] ?>"
                                       class="block text-center
                                              border border-red-600
                                              text-red-600 rounded-lg
                                              py-2 text-sm font-semibold
                                              hover:bg-red-50">

                                        Xem chi tiết

                                    </a>

                                    <form action="/cart/add" method="POST">

                                        <input type="hidden"
                                               name="product_id"
                                               value="<?= (int) $product['id'] ?>">

                                        <input type="hidden"
                                               name="quantity"
                                               value="1">

                                        <button type="submit"
                                                class="w-full bg-red-600
                                                       hover:bg-red-700
                                                       text-white rounded-lg
                                                       py-3 text-sm
                                                       font-semibold
                                                       flex items-center
                                                       justify-center gap-2">

                                            <svg class="w-4 h-4"
                                                 fill="none"
                                                 stroke="currentColor"
                                                 viewBox="0 0 24 24">

                                                <path stroke-width="2"
                                                      stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      d="M3 3h2l2 12h12l2-9H6M9 21h.01M18 21h.01"/>

                                            </svg>

                                            Thêm vào giỏ

                                        </button>

                                    </form>

                                </div>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            </section>

        </div>

    </div>

</div>

<!-- Sắp xếp sản phẩm bằng JavaScript -->
<script>

    const sortSelect = document.getElementById('sortProducts');
    const productList = document.getElementById('productList');

    const originalProducts = Array.from(productList.children);

    sortSelect.addEventListener('change', function () {

        const products = [...originalProducts];

        if (this.value === 'price-asc') {

            products.sort((a, b) =>
                Number(a.dataset.price) -
                Number(b.dataset.price)
            );

        } else if (this.value === 'price-desc') {

            products.sort((a, b) =>
                Number(b.dataset.price) -
                Number(a.dataset.price)
            );

        } else if (this.value === 'best-seller') {

            products.sort((a, b) =>
                Number(b.dataset.sold) -
                Number(a.dataset.sold)
            );

        }

        products.forEach(product => {
            productList.appendChild(product);
        });

    });

</script>
