<?php
declare(strict_types=1);

namespace App\Services;

class CartService
{
    // Khởi tạo giỏ hàng trong Session
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    // Lấy danh sách sản phẩm trong giỏ
    public function getItems(): array
    {
        return array_values($_SESSION['cart']);
    }

    // Thêm sản phẩm vào giỏ hàng
    public function add(array $product, int $quantity = 1): void
    {
        $id = (int) ($product['id'] ?? 0);

        if ($id <= 0 || $quantity <= 0) {
            return;
        }

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$id] = [
                'id' => $id,
                'name' => (string) ($product['name'] ?? ''),
                'price' => (float) ($product['price'] ?? 0),
                'image' => (string) ($product['image'] ?? ''),
                'quantity' => $quantity
            ];
        }
    }

    // Cập nhật số lượng sản phẩm
    public function update(int $productId, int $quantity): void
    {
        if (!isset($_SESSION['cart'][$productId])) {
            return;
        }

        if ($quantity <= 0) {
            $this->remove($productId);
            return;
        }

        $_SESSION['cart'][$productId]['quantity'] = $quantity;
    }

    // Xóa một sản phẩm khỏi giỏ hàng
    public function remove(int $productId): void
    {
        unset($_SESSION['cart'][$productId]);
    }

    // Tính tổng tiền giỏ hàng
    public function getTotal(): float
    {
        $total = 0;

        foreach ($this->getItems() as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $total;
    }

    // Xóa toàn bộ giỏ hàng
    public function clear(): void
    {
        $_SESSION['cart'] = [];
    }
}

declare(strict_types=1);

namespace app\Services;

use app\Models\{Cart, CheckoutInventory};
use app\Support\{Database, Money};
use DomainException;
use InvalidArgumentException;
use PDO;

final class CartService
{
    public function __construct(
        private PDO $db,
        private Cart $cart,
        private CheckoutInventory $inventory
    ) {
    }

    public function change(int $userId, int $productId, ?int $quantity, string $action): void
    {
        Database::transaction($this->db, function () use ($userId, $productId, $quantity, $action): void {
            $cartId = $this->cart->lock($userId);
            $current = $this->cart->quantity($cartId, $productId);

            if ($action === 'remove') {
                $this->cart->remove($cartId, $productId);
                return;
            }

            if ($action === 'update' && $current === null) {
                throw new DomainException('Sản phẩm không có trong giỏ của bạn.');
            }

            $product = $this->inventory->lock($productId);
            $next = $quantity + ($action === 'add' ? ($current ?? 0) : 0);

            if ($next > $product['stock']) {
                throw new DomainException('Số lượng yêu cầu vượt tồn kho hiện tại.');
            }

            $this->cart->save($cartId, $productId, $next);
        });
    }

    public static function summary(array $items): array
    {
        $total = 0;
        $canCheckout = $items !== [];

        try {
            foreach ($items as $item) {
                $valid = $item['available'] && $item['quantity'] <= $item['stock'];
                $canCheckout = $canCheckout && $valid;
                $total = Money::addLine($total, $item['price'], (int) $item['quantity']);
            }

            return ['total' => Money::toDecimal($total), 'canCheckout' => $canCheckout];
        } catch (InvalidArgumentException) {
            return ['total' => null, 'canCheckout' => false];
        }
    }
}
