<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Cart;
use App\Models\CheckoutInventory;

use App\Support\Database;
use App\Support\Money;

use DomainException;
use InvalidArgumentException;
use OverflowException;
use PDO;

final class CartService
{
    // ==========================================
    // 1. KHOI TAO SERVICE
    // ==========================================

    public function __construct(
        private PDO $db,
        private Cart $cart,
        private CheckoutInventory $inventory
    ) {
    }

    // ==========================================
    // 2. THEM, CAP NHAT VA XOA SAN PHAM
    // ==========================================

    public function change(
        int $userId,
        int $productId,
        ?int $quantity,
        string $action
    ): void {

        // Kiem tra ID.
        if ($userId <= 0 || $productId <= 0) {
            throw new InvalidArgumentException(
                'Thông tin người dùng hoặc sản phẩm không hợp lệ.'
            );
        }

        // Kiem tra hanh dong.
        if (!in_array(
            $action,
            ['add', 'update', 'remove'],
            true
        )) {
            throw new InvalidArgumentException(
                'Thao tác giỏ hàng không hợp lệ.'
            );
        }

        // Kiem tra so luong.
        if (
            $action !== 'remove'
            && (
                $quantity === null
                || $quantity < 1
                || $quantity > 1000000
            )
        ) {
            throw new InvalidArgumentException(
                'Số lượng sản phẩm không hợp lệ.'
            );
        }

        // Su dung Transaction de bao dam du lieu.
        Database::transaction(
            $this->db,
            function () use (
                $userId,
                $productId,
                $quantity,
                $action
            ): void {

                // Khoa gio hang cua nguoi dung.
                $cartId = $this->cart->lock(
                    $userId
                );

                // Lay so luong hien tai.
                $current = $this->cart->quantity(
                    $cartId,
                    $productId
                );

                // Xoa san pham.
                if ($action === 'remove') {

                    $this->cart->remove(
                        $cartId,
                        $productId
                    );

                    return;
                }

                // Kiem tra san pham trong gio.
                if (
                    $action === 'update'
                    && $current === null
                ) {
                    throw new DomainException(
                        'Sản phẩm không có trong giỏ của bạn.'
                    );
                }

                // Khoa va kiem tra ton kho.
                $product = $this->inventory->lock(
                    $productId
                );

                if (
                    !is_array($product)
                    || !isset($product['stock'])
                ) {
                    throw new DomainException(
                        'Sản phẩm không tồn tại hoặc không khả dụng.'
                    );
                }

                // Lay so luong ton kho.
                $stock = (int) $product['stock'];

                // Kiem tra san pham kha dung.
                if (
                    isset($product['available'])
                    && !$product['available']
                ) {
                    throw new DomainException(
                        'Sản phẩm đã ngừng kinh doanh.'
                    );
                }

                // Tinh so luong moi.
                $next = (int) $quantity;

                if ($action === 'add') {
                    $next += (int) ($current ?? 0);
                }

                // Kiem tra so luong hop le.
                if ($next < 1 || $next > 1000000) {

                    throw new DomainException(
                        'Số lượng sản phẩm vượt giới hạn.'
                    );
                }

                // Kiem tra ton kho.
                if ($next > $stock) {

                    throw new DomainException(
                        'Số lượng yêu cầu vượt tồn kho hiện tại.'
                    );
                }

                // Luu gio hang vao database.
                $this->cart->save(
                    $cartId,
                    $productId,
                    $next
                );
            }
        );
    }

    // ==========================================
    // 3. TINH TONG TIEN GIO HANG
    // ==========================================

    public static function summary(
        array $items
    ): array {

        $total = 0;

        $canCheckout = $items !== [];

        try {

            foreach ($items as $item) {

                // Lay thong tin san pham.
                $quantity = (int) (
                    $item['quantity'] ?? 0
                );

                $stock = (int) (
                    $item['stock'] ?? 0
                );

                $available = (bool) (
                    $item['available'] ?? false
                );

                // Kiem tra du lieu gia.
                if (!isset($item['price'])) {

                    throw new InvalidArgumentException(
                        'Không tìm thấy giá sản phẩm.'
                    );
                }

                $price = (string) $item['price'];

                // Kiem tra dieu kien thanh toan.
                $valid = $available
                    && $quantity > 0
                    && $quantity <= $stock
                    && $quantity <= 1000000;

                $canCheckout = $canCheckout && $valid;

                // Cong don tong tien.
                $total = Money::addLine(
                    $total,
                    $price,
                    $quantity
                );
            }

            // Tra ve ket qua.
            return [
                'total' => Money::toDecimal($total),
                'canCheckout' => $canCheckout,
            ];

        } catch (
            InvalidArgumentException |
            OverflowException $error
        ) {

            return [
                'total' => null,
                'canCheckout' => false,
            ];
        }
    }
}