
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

</header>
