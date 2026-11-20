
<header class="site-header">

    <!-- THANH THÔNG TIN -->
    <div class="header-top">
        <div class="container-custom flex justify-between flex-wrap gap-2">

            <span>
                CT TECH STORE - Mua sắm công nghệ trực tuyến
            </span>

            <span>
                Giao hàng toàn quốc | Hỗ trợ bảo hành
            </span>

        </div>
    </div>

    <!-- HEADER CHÍNH -->
    <div class="header-main">

        <div class="container-custom header-inner">

            <!-- LOGO -->
            <a href="/" class="logo">
                CT TECH STORE
            </a>

            <!-- TÌM KIẾM -->
            <form action="/products"
                  method="GET"
                  class="search-box">

                <input type="search"
                       name="search"
                       placeholder="Bạn muốn tìm sản phẩm gì?"
                       aria-label="Tìm kiếm sản phẩm">

                <button type="submit" aria-label="Tìm kiếm">

                    <svg width="24"
                         height="24"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2">

                        <circle cx="10.5" cy="10.5" r="7"/>
                        <path d="m16 16 5 5"/>

                    </svg>

                </button>

            </form>

            <!-- TÀI KHOẢN VÀ GIỎ HÀNG -->
            <div class="header-actions">

                <a href="/login">

                    <svg width="28"
                         height="28"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.8">

                        <circle cx="12" cy="7" r="4"/>
                        <path d="M4 22v-3a8 8 0 0 1 16 0v3"/>

                    </svg>

                    <span>Tài khoản</span>

                </a>

                <a href="/cart">

                    <svg width="28"
                         height="28"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.8">

                        <path d="M2 3h3l3 12h11l3-9H6"/>
                        <circle cx="9" cy="20" r="1.5"/>
                        <circle cx="19" cy="20" r="1.5"/>

                    </svg>

                    <span>Giỏ hàng</span>

                </a>

            </div>

        </div>

    </div>

    <!-- MENU ĐIỀU HƯỚNG -->
    <nav class="navigation">

        <div class="container-custom">

            <ul>

                <li>
                    <a href="/">Trang chủ</a>
                </li>

                <li>
                    <a href="/products">Tất cả sản phẩm</a>
                </li>

                <li>
                    <a href="/products?category=dien-thoai">
                        Điện thoại
                    </a>
                </li>

                <li>
                    <a href="/products?category=laptop">
                        Laptop
                    </a>
                </li>

                <li>
                    <a href="/products?category=man-hinh">
                        Màn hình
                    </a>
                </li>

                <li>
                    <a href="/products?category=tai-nghe">
                        Tai nghe
                    </a>
                </li>

                <li>
                    <a href="/products?category=ban-phim">
                        Bàn phím
                    </a>
                </li>

                <li>
                    <a href="/products?category=phu-kien">
                        Phụ kiện
                    </a>
                </li>

            </ul>

        </div>

    </nav>

<?php
use App\Support\View;
?>

<header class="bg-black text-white shadow-lg">

    <!-- Thanh thông tin -->
    <div class="bg-[#EF233C] text-white text-sm">
        <div class="container-custom py-2 flex
                    justify-between items-center">
            <span>CT TECH STORE - Điện máy chính hãng</span>
            <span class="hidden md:block">
                Giao hàng toàn quốc
            </span>
        </div>
    </div>

    <!-- Logo, tìm kiếm và tài khoản -->
    <div class="container-custom py-5 flex flex-wrap
                items-center justify-between gap-4">

        <a href="/" class="text-2xl font-extrabold">
            CT <span class="text-[#EF233C]">TECH STORE</span>
        </a>

        <form action="/products" method="GET"
              class="flex flex-1 max-w-xl">
            <input
                type="search"
                name="search"
                placeholder="Tìm điện thoại, laptop, phụ kiện..."
                class="w-full px-4 py-3 rounded-l-lg
                       text-black outline-none"
            >
            <button type="submit"
                    class="bg-[#EF233C] px-5 rounded-r-lg
                           hover:bg-red-700">
                Tìm kiếm
            </button>
        </form>

        <div class="flex items-center gap-5">
            <a href="/login" class="hover:text-red-400">
                Đăng nhập
            </a>

            <a href="/cart" class="hover:text-red-400">
                🛒 Giỏ hàng
            </a>
        </div>
    </div>

    <!-- Menu điều hướng -->
    <nav class="bg-gray-900">
        <div class="container-custom flex flex-wrap
                    gap-6 py-4 font-medium">

            <a href="/" class="hover:text-red-400">
                Trang chủ
            </a>

            <a href="/products" class="hover:text-red-400">
                Sản phẩm
            </a>

            <a href="/products?category=phone"
               class="hover:text-red-400">
                Điện thoại
            </a>

            <a href="/products?category=laptop"
               class="hover:text-red-400">
                Laptop
            </a>

            <a href="/products?category=accessories"
               class="hover:text-red-400">
                Phụ kiện
            </a>

            <a href="/contact" class="hover:text-red-400">
                Liên hệ
            </a>

        </div>
    </nav>
</header>
