
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
