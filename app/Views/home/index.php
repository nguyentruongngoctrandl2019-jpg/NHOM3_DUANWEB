
<?php
use App\Support\View;

$products = $products ?? [];

$saleProducts = $saleProducts ?? array_slice($products, 0, 4);
$bestSellers = $bestSellers ?? array_slice($products, 0, 4);
$featuredProducts = $featuredProducts ?? array_slice($products, 0, 8);
?>

<div class="container-custom py-8">

    <!-- BANNER TRANG CHỦ -->
    <section class="hero-banner mb-8">

        <div>
            <p class="font-bold uppercase">
                CT TECH STORE
            </p>

            <h1>
                Công nghệ hiện đại
                <br>
                Nâng tầm cuộc sống
            </h1>

            <p>
                Khám phá các sản phẩm công nghệ
                phù hợp với nhu cầu của bạn.
            </p>

            <a href="/products" class="btn-primary">
                Mua sắm ngay
            </a>
        </div>

    </section>

    <!-- DANH MỤC SẢN PHẨM -->
    <section class="mb-10">

        <h2 class="section-title">
            Danh mục sản phẩm
        </h2>

        <?php
        $homeCategories = [
            ['name' => 'Điện thoại', 'slug' => 'dien-thoai', 'icon' => 'M7 2h10v20H7z M10 18h4'],
            ['name' => 'Laptop', 'slug' => 'laptop', 'icon' => 'M4 3h16v14H4z M2 20h20'],
            ['name' => 'Màn hình', 'slug' => 'man-hinh', 'icon' => 'M2 3h20v14H2z M12 17v5 M7 22h10'],
            ['name' => 'Tai nghe', 'slug' => 'tai-nghe', 'icon' => 'M3 14v-3a9 9 0 0 1 18 0v3 M3 13h5v8H3z M16 13h5v8h-5z'],
            ['name' => 'Bàn phím', 'slug' => 'ban-phim', 'icon' => 'M2 5h20v14H2z M5 9h2 M9 9h2 M13 9h2 M17 9h2 M6 15h12'],
            ['name' => 'Phụ kiện', 'slug' => 'phu-kien', 'icon' => 'M12 2a7 7 0 0 1 7 7v6a7 7 0 0 1-14 0V9a7 7 0 0 1 7-7z M12 2v7']
        ];
        ?>

        <div class="category-grid">

            <?php foreach ($homeCategories as $category): ?>

                <a href="/products?category=<?= View::e($category['slug']) ?>"
                   class="category-card">

                    <svg viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.5"
                         stroke-linecap="round"
                         stroke-linejoin="round">

                        <path d="<?= View::e($category['icon']) ?>"/>

                    </svg>

                    <strong>
                        <?= View::e($category['name']) ?>
                    </strong>

                </a>

            <?php endforeach; ?>

        </div>

    </section>

    <!-- CÁC NHÓM SẢN PHẨM -->
    <?php
    $sections = [
        [
            'title' => 'Sản phẩm giảm giá',
            'products' => $saleProducts
        ],
        [
            'title' => 'Sản phẩm bán chạy',
            'products' => $bestSellers
        ],
        [
            'title' => 'Sản phẩm nổi bật',
            'products' => $featuredProducts
        ],
        [
            'title' => 'Tất cả sản phẩm',
            'products' => $products
        ]
    ];
    ?>

    <?php foreach ($sections as $section): ?>

        <section class="mb-12">

            <div class="flex items-center justify-between flex-wrap gap-3 mb-5">

                <h2 class="section-title">
                    <?= View::e($section['title']) ?>
                </h2>

                <a href="/products" class="btn-outline">
                    Xem tất cả
                </a>

            </div>

            <div class="product-grid">

                <?php foreach ($section['products'] as $product): ?>

                    <?php
                    $id = (int) ($product['id'] ?? 0);
                    $name = (string) ($product['name'] ?? '');
                    $price = (float) ($product['price'] ?? 0);
                    $image = (string) ($product['image'] ?? '');
                    $oldPrice = (float) ($product['old_price'] ?? 0);
                    ?>

                    <article class="product-card">

                        <!-- HÌNH ẢNH -->
                        <a href="/products/<?= $id ?>">

                            <?php if ($image !== ''): ?>

                                <img
                                    src="<?= View::e($image) ?>"
                                    alt="<?= View::e($name) ?>"
                                    class="product-image"
                                    loading="lazy"
                                >

                            <?php else: ?>

                                <div class="product-image bg-gray-100 rounded-lg flex items-center justify-center">

                                    <svg width="65"
                                         height="65"
                                         viewBox="0 0 24 24"
                                         fill="none"
                                         stroke="#9ca3af"
                                         stroke-width="1.5">

                                        <rect x="3" y="3"
                                              width="18"
                                              height="18"
                                              rx="2"/>

                                        <circle cx="8" cy="8" r="2"/>

                                        <path d="m3 17 5-5 4 4 4-6 5 7"/>

                                    </svg>

                                </div>

                            <?php endif; ?>

                        </a>

                        <!-- TÊN SẢN PHẨM -->
                        <h3 class="product-name">
                            <?= View::e($name) ?>
                        </h3>

                        <!-- GIÁ CŨ -->
                        <?php if ($oldPrice > $price): ?>

                            <p class="old-price">
                                <?= View::money($oldPrice) ?>
                            </p>

                        <?php endif; ?>

                        <!-- GIÁ BÁN -->
                        <p class="product-price">
                            <?= View::money($price) ?>
                        </p>

                        <!-- NÚT MUA HÀNG -->
                        <div class="product-actions">

                            <a href="/products/<?= $id ?>"
                               class="btn-outline">
                                Chi tiết
                            </a>

                            <form action="/cart/add" method="POST">

                                <input type="hidden"
                                       name="product_id"
                                       value="<?= $id ?>">

                                <input type="hidden"
                                       name="quantity"
                                       value="1">

                                <button type="submit"
                                        class="btn-primary">
                                    Thêm giỏ
                                </button>

                            </form>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

            <?php if (empty($section['products'])): ?>

                <p class="bg-white p-6 rounded-lg text-center">
                    Sản phẩm đang được cập nhật.
                </p>

            <?php endif; ?>

        </section>

    <?php endforeach; ?>

    <!-- BANNER PHỤ -->
    <section class="bg-gray-900 text-white rounded-xl p-10 mb-10">

        <h2 class="text-3xl font-bold mb-4">
            Khám phá thế giới công nghệ
        </h2>

        <p class="mb-6">
            Điện thoại, laptop và phụ kiện dành cho
            học tập, làm việc và giải trí.
        </p>

        <a href="/products" class="btn-primary">
            Khám phá ngay
        </a>

    </section>

    <!-- CAM KẾT CỬA HÀNG -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-10">

        <div class="bg-white rounded-xl p-6 text-center shadow-sm">
            <h3 class="text-lg font-bold text-red-600 mb-2">
                Sản phẩm chính hãng
            </h3>
            <p>Thông tin nguồn gốc rõ ràng.</p>
        </div>

        <div class="bg-white rounded-xl p-6 text-center shadow-sm">
            <h3 class="text-lg font-bold text-red-600 mb-2">
                Giao hàng toàn quốc
            </h3>
            <p>Mua sắm trực tuyến tiện lợi.</p>
        </div>

        <div class="bg-white rounded-xl p-6 text-center shadow-sm">
            <h3 class="text-lg font-bold text-red-600 mb-2">
                Chính sách bảo hành
            </h3>
            <p>Hỗ trợ bảo hành sản phẩm.</p>
        </div>

        <div class="bg-white rounded-xl p-6 text-center shadow-sm">
            <h3 class="text-lg font-bold text-red-600 mb-2">
                Hỗ trợ khách hàng
            </h3>
            <p>Tư vấn lựa chọn sản phẩm.</p>
        </div>

    </section>

    <!-- GIỚI THIỆU CỬA HÀNG -->
    <section class="bg-white rounded-xl p-8 mb-10">

        <h2 class="section-title">
            Về CT TECH STORE
        </h2>

        <p class="leading-8 text-gray-600">
            CT TECH STORE là website bán hàng điện máy
            trực tuyến tại Cần Thơ, cung cấp các sản phẩm
            điện thoại, laptop, màn hình và phụ kiện công nghệ.
            Chúng tôi hướng đến trải nghiệm mua sắm thuận tiện,
            giao diện dễ sử dụng và hỗ trợ khách hàng trên toàn quốc.
        </p>

    </section>

</div>
