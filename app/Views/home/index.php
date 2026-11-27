
<?php
use App\Support\View;

$pageTitle = 'Trang chủ - CT TECH STORE';

$products = $products ?? [];
$saleProducts = $saleProducts ?? [];
$bestSellers = $bestSellers ?? [];
$featuredProducts = $featuredProducts ?? [];

$homeCategories = [
    ['name' => 'Điện thoại', 'slug' => 'dien-thoai', 'icon' => 'M7 2h10v20H7z M10 18h4'],
    ['name' => 'Laptop', 'slug' => 'laptop', 'icon' => 'M4 3h16v14H4z M2 20h20'],
    ['name' => 'Màn hình', 'slug' => 'man-hinh', 'icon' => 'M2 3h20v14H2z M12 17v5 M7 22h10'],
    ['name' => 'Tai nghe', 'slug' => 'tai-nghe', 'icon' => 'M3 14v-3a9 9 0 0 1 18 0v3 M3 13h5v8H3z M16 13h5v8h-5z'],
    ['name' => 'Bàn phím', 'slug' => 'ban-phim', 'icon' => 'M2 5h20v14H2z M5 9h2 M9 9h2 M13 9h2 M17 9h2 M6 15h12'],
    ['name' => 'Phụ kiện', 'slug' => 'phu-kien', 'icon' => 'M12 2a7 7 0 0 1 7 7v6a7 7 0 0 1-14 0V9a7 7 0 0 1 7-7z M12 2v7']
];

$sections = [
    [
        'title' => 'Sản phẩm giảm giá',
        'class' => 'sale-section',
        'products' => array_slice($saleProducts, 0, 4)
    ],
    [
        'title' => 'Sản phẩm bán chạy',
        'class' => 'bestseller-section',
        'products' => array_slice($bestSellers, 0, 4)
    ],
    [
        'title' => 'Sản phẩm nổi bật',
        'class' => 'featured-section',
        'products' => array_slice($featuredProducts, 0, 4)
    ],
    [
        'title' => 'Tất cả sản phẩm',
        'class' => 'all-products-section',
        'products' => $products
    ]
];
?>

<div class="container-custom home-page">

    <!-- 1. BANNER -->
    <section class="home-slider" id="homeSlider">

        <div class="banner-slide slide-one active">
            <div class="banner-content">
                <span class="banner-label">
                    CT TECH STORE
                </span>

                <h1>
                    Công nghệ hiện đại<br>
                    Nâng tầm cuộc sống
                </h1>

                <p>
                    Khám phá các sản phẩm công nghệ
                    chính hãng với mức giá hấp dẫn.
                </p>

                <a href="/products" class="banner-button">
                    Mua sắm ngay
                </a>
            </div>

            <div class="banner-picture">
                <img src="https://images.unsplash.com/photo-1498049794561-7780e7231661?w=900"
                     alt="Sản phẩm công nghệ">
            </div>
        </div>

        <div class="banner-slide slide-two">
            <div class="banner-content">
                <span class="banner-label">
                    LAPTOP CHÍNH HÃNG
                </span>

                <h2>
                    Hiệu năng mạnh mẽ<br>
                    Làm việc hiệu quả
                </h2>

                <p>
                    Laptop dành cho học tập,
                    làm việc và giải trí.
                </p>

                <a href="/products?category=laptop"
                   class="banner-button">
                    Khám phá ngay
                </a>
            </div>

            <div class="banner-picture">
                <img src="https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=900"
                     alt="Laptop chính hãng">
            </div>
        </div>

        <div class="banner-slide slide-three">
            <div class="banner-content">
                <span class="banner-label">
                    PHỤ KIỆN CÔNG NGHỆ
                </span>

                <h2>
                    Trải nghiệm tuyệt vời<br>
                    Giá cả hợp lý
                </h2>

                <p>
                    Tai nghe, bàn phím và các
                    phụ kiện công nghệ tiện ích.
                </p>

                <a href="/products?category=phu-kien"
                   class="banner-button">
                    Xem sản phẩm
                </a>
            </div>

            <div class="banner-picture">
                <img src="https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=900"
                     alt="Phụ kiện công nghệ">
            </div>
        </div>

        <!-- Nút chuyển banner -->
        <button type="button"
                class="slider-prev"
                aria-label="Banner trước">
            &#10094;
        </button>

        <button type="button"
                class="slider-next"
                aria-label="Banner tiếp theo">
            &#10095;
        </button>

        <div class="slider-dots">
            <button type="button" class="active"
                    aria-label="Banner 1"></button>
            <button type="button"
                    aria-label="Banner 2"></button>
            <button type="button"
                    aria-label="Banner 3"></button>
        </div>

    </section>

    <!-- 2. DANH MỤC NỔI BẬT -->
    <section class="home-section">

        <div class="section-heading">
            <h2>Danh mục nổi bật</h2>
        </div>

        <div class="category-grid">

            <?php foreach ($homeCategories as $category): ?>

                <a href="/products?category=<?= View::e($category['slug']) ?>"
                   class="category-card">

                    <div class="category-icon">
                        <svg width="42"
                             height="42"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="1.5"
                             stroke-linecap="round"
                             stroke-linejoin="round">

                            <path d="<?= View::e($category['icon']) ?>"/>

                        </svg>
                    </div>

                    <strong>
                        <?= View::e($category['name']) ?>
                    </strong>

                </a>

            <?php endforeach; ?>

        </div>

    </section>

    <!-- 3. CÁC NHÓM SẢN PHẨM -->

    <?php foreach ($sections as $section): ?>

        <section class="home-section <?= View::e($section['class']) ?>">

            <div class="section-heading">

                <h2>
                    <?= View::e($section['title']) ?>
                </h2>

                <a href="/products" class="view-all">
                    Xem tất cả &#8594;
                </a>

            </div>

            <div class="product-grid">

                <?php foreach ($section['products'] as $product): ?>

                    <?php
                    $id = (int) ($product['id'] ?? 0);
                    $name = (string) ($product['name'] ?? '');
                    $price = (string) ($product['price'] ?? '0');
                    $oldPrice = (string) ($product['old_price'] ?? '0');

                    $image = (string) (
                        $product['image_url']
                        ?? $product['image']
                        ?? ''
                    );
                    ?>

                    <article class="product-card">

                        <!-- Ảnh sản phẩm -->
                        <a href="/products/<?= $id ?>"
                           class="product-image-box">

                            <?php if ($image !== ''): ?>

                                <img src="<?= View::e($image) ?>"
                                     alt="<?= View::e($name) ?>"
                                     class="product-image"
                                     loading="lazy">

                            <?php else: ?>

                                <div class="product-placeholder">

                                    <svg width="60"
                                         height="60"
                                         viewBox="0 0 24 24"
                                         fill="none"
                                         stroke="currentColor"
                                         stroke-width="1.5">

                                        <rect x="3" y="3"
                                              width="18" height="18"
                                              rx="2"/>

                                        <circle cx="8" cy="8" r="2"/>

                                        <path d="m3 17 5-5 4 4 4-6 5 7"/>

                                    </svg>

                                    <span>Chưa có ảnh</span>

                                </div>

                            <?php endif; ?>

                        </a>

                        <!-- Thông tin sản phẩm -->
                        <div class="product-info">

                            <h3 class="product-name">

                                <a href="/products/<?= $id ?>">
                                    <?= View::e($name) ?>
                                </a>

                            </h3>

                            <div class="product-prices">

                                <p class="product-price">
                                    <?= View::money($price) ?>
                                </p>

                                <?php if ($oldPrice > $price): ?>

                                    <p class="old-price">
                                        <?= View::money($oldPrice) ?>
                                    </p>

                                <?php endif; ?>

                            </div>

                            <div class="product-actions">

                                <a href="/products/<?= $id ?>"
                                   class="btn-outline">
                                    Chi tiết
                                </a>

                                <form action="/cart/add"
                                      method="POST">

                                    <input type="hidden"
                                           name="product_id"
                                           value="<?= $id ?>">

                                    <input type="hidden"
                                           name="quantity"
                                           value="1">

                                    <button type="submit"
                                            class="btn-primary">

                                        <svg width="18"
                                             height="18"
                                             viewBox="0 0 24 24"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="2">

                                            <path d="M2 3h3l3 12h11l3-9H6"/>
                                            <circle cx="9" cy="20" r="1"/>
                                            <circle cx="19" cy="20" r="1"/>

                                        </svg>

                                        Thêm giỏ

                                    </button>

                                </form>

                            </div>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

            <?php if (empty($section['products'])): ?>

                <div class="empty-products">
                    Sản phẩm đang được cập nhật.
                </div>

            <?php endif; ?>

        </section>

    <?php endforeach; ?>

    <!-- 4. CAM KẾT CỬA HÀNG -->

    <section class="store-benefits">

        <div class="benefit-card">
            <h3>Sản phẩm chính hãng</h3>
            <p>Nguồn gốc sản phẩm rõ ràng.</p>
        </div>

        <div class="benefit-card">
            <h3>Giao hàng toàn quốc</h3>
            <p>Mua sắm trực tuyến tiện lợi.</p>
        </div>

        <div class="benefit-card">
            <h3>Hỗ trợ bảo hành</h3>
            <p>Chính sách bảo hành sản phẩm.</p>
        </div>

        <div class="benefit-card">
            <h3>Hỗ trợ khách hàng</h3>
            <p>Tư vấn lựa chọn sản phẩm.</p>
        </div>

    </section>

    <!-- 5. GIỚI THIỆU -->

    <section class="store-about">

        <h2>Về CT TECH STORE</h2>

        <p>
            CT TECH STORE là website bán hàng điện máy
            trực tuyến tại Cần Thơ, chuyên cung cấp
            điện thoại, laptop, màn hình và phụ kiện
            công nghệ. Chúng tôi hướng đến trải nghiệm
            mua sắm thuận tiện và hỗ trợ khách hàng
            trên toàn quốc.
        </p>

    </section>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const slider = document.querySelector('#homeSlider');

    if (!slider) return;

    const slides = slider.querySelectorAll('.banner-slide');
    const dots = slider.querySelectorAll('.slider-dots button');
    const prev = slider.querySelector('.slider-prev');
    const next = slider.querySelector('.slider-next');

    let current = 0;

    // Hiển thị banner
    function showSlide(index) {

        current = (index + slides.length) % slides.length;

        slides.forEach(function (slide) {
            slide.classList.remove('active');
        });

        dots.forEach(function (dot) {
            dot.classList.remove('active');
        });

        slides[current].classList.add('active');
        dots[current].classList.add('active');
    }

    // Chuyển ảnh tiếp theo
    next.addEventListener('click', function () {
        showSlide(current + 1);
    });

    // Quay lại ảnh trước
    prev.addEventListener('click', function () {
        showSlide(current - 1);
    });

    // Chuyển ảnh bằng dấu chấm
    dots.forEach(function (dot, index) {
        dot.addEventListener('click', function () {
            showSlide(index);
        });
    });

    // Tự động chuyển ảnh sau 5 giây
    setInterval(function () {
        showSlide(current + 1);
    }, 5000);

});
</script>

