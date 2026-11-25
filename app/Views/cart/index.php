
<?php
use App\Support\View;

$items = $items ?? [];
$total = $total ?? 0;
?>

<section class="container-custom py-10">

    <h1 class="text-3xl font-bold mb-8">
        Giỏ hàng của bạn
    </h1>

    <?php if (empty($items)): ?>

        <div class="bg-white rounded-xl p-10 text-center shadow-sm">
            <h2 class="text-xl font-semibold mb-4">
                Giỏ hàng đang trống
            </h2>

            <p class="text-gray-600 mb-6">
                Bạn chưa có sản phẩm nào trong giỏ hàng.
            </p>

            <a href="/products" class="btn-primary">
                Tiếp tục mua sắm
            </a>
        </div>

    <?php else: ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- DANH SÁCH SẢN PHẨM -->
            <div class="lg:col-span-2 space-y-4">

                <?php foreach ($items as $item): ?>
                    <?php
                    $id = (int) ($item['id'] ?? 0);
                    $name = (string) ($item['name'] ?? '');
                    $price = (float) ($item['price'] ?? 0);
                    $quantity = (int) ($item['quantity'] ?? 1);
                    $subtotal = $price * $quantity;
                    ?>

                    <div class="bg-white p-6 rounded-xl shadow-sm">

                        <h2 class="text-xl font-bold mb-3">
                            <?= View::e($name) ?>
                        </h2>

                        <p class="text-red-600 text-lg font-bold mb-4">
                            <?= View::money($price) ?>
                        </p>

                        <!-- CẬP NHẬT SỐ LƯỢNG -->
                        <form action="/cart/update"
                              method="POST"
                              class="flex flex-wrap items-center gap-3 mb-4">

                            <input type="hidden"
                                   name="product_id"
                                   value="<?= $id ?>">

                            <label class="font-medium">
                                Số lượng:
                            </label>

                            <input type="number"
                                   name="quantity"
                                   value="<?= $quantity ?>"
                                   min="1"
                                   max="10"
                                   required
                                   class="w-20 border border-gray-300 rounded-lg px-3 py-2">

                            <button type="submit"
                                    class="bg-gray-900 text-white px-5 py-2 rounded-lg hover:bg-gray-700">
                                Cập nhật
                            </button>
                        </form>

                        <!-- XÓA SẢN PHẨM -->
                        <form action="/cart/remove"
                              method="POST"
                              class="mb-4">

                            <input type="hidden"
                                   name="product_id"
                                   value="<?= $id ?>">

                            <button type="submit"
                                    class="bg-red-600 text-white px-5 py-2 rounded-lg hover:bg-red-700">
                                Xóa sản phẩm
                            </button>
                        </form>

                        <div class="border-t pt-4">
                            <p class="text-gray-700">
                                Thành tiền:
                                <strong class="text-red-600">
                                    <?= View::money($subtotal) ?>
                                </strong>
                            </p>
                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

            <!-- TỔNG ĐƠN HÀNG -->
            <div class="bg-white rounded-xl p-6 shadow-sm h-fit">

                <h2 class="text-xl font-bold mb-5">
                    Tổng đơn hàng
                </h2>

                <div class="flex justify-between mb-5">
                    <span>Tạm tính:</span>
                    <strong>
                        <?= View::money($total) ?>
                    </strong>
                </div>

                <div class="border-t pt-5 flex justify-between">
                    <span class="font-bold">
                        Tổng cộng:
                    </span>

                    <strong class="text-red-600 text-xl">
                        <?= View::money($total) ?>
                    </strong>
                </div>

                <a href="/checkout"
                   class="btn-primary block text-center mt-6">
                    Tiến hành thanh toán
                </a>

            </div>

        </div>

    <?php endif; ?>

</section>
