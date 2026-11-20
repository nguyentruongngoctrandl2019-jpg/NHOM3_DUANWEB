
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
