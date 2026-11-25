
<header class="site-header">

    <!-- THANH THÔNG TIN -->
    <div class="header-top">
        <div class="container-custom flex justify-between flex-wrap gap-2">
            <span>CT TECH STORE - Công nghệ cho cuộc sống</span>
            <span>Giao hàng toàn quốc | Hỗ trợ khách hàng</span>
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
            <form action="/products" method="GET" class="search-box">

                <input type="text"
                       name="search"
                       placeholder="Bạn muốn tìm sản phẩm gì?">

                <button type="submit" aria-label="Tìm kiếm">
                    <svg width="24" height="24"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2">
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m20 20-4-4"/>
                    </svg>
                </button>

            </form>

            <!-- TÀI KHOẢN VÀ GIỎ HÀNG -->
            <div class="header-actions">

                <a href="/login" class="flex items-center gap-2">

                    <svg width="26" height="26"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M4 21v-2a8 8 0 0 1 16 0v2"/>
                    </svg>

                    <span>Đăng nhập</span>

                </a>

                <a href="/cart" class="flex items-center gap-2">

                    <svg width="26" height="26"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2">
                        <path d="M2 3h3l3 12h11l3-9H6"/>
                        <circle cx="9" cy="20" r="1"/>
                        <circle cx="19" cy="20" r="1"/>
                    </svg>

                    <span>Giỏ hàng</span>

                </a>

            </div>
        </div>
    </div>

    <!-- MENU DANH MỤC -->
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

</header>
