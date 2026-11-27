<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\{Cart, CheckoutInventory};
use App\Support\{Database, Money};
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