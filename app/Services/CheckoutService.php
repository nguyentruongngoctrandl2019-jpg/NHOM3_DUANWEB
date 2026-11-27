<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\{Cart, CheckoutInventory, Order};
use App\Support\{Database, Money};
use DomainException;
use PDO;
use PDOException;

final class CheckoutService
{
    public function __construct(
        private PDO $db,
        private Cart $cart,
        private Order $orders,
        private CheckoutInventory $inventory
    ) {
    }

    public function place(int $userId, string $token, array $contact): string
    {
        try {
            return Database::transaction($this->db, function () use ($userId, $token, $contact): string {
                $cartId = $this->cart->lock($userId);

                if ($this->orders->findOwned($token, $userId) !== null) {
                    return $token;
                }

                $items = $this->cart->activeItems($userId);

                if ($items === []) {
                    throw new DomainException('Giỏ hàng đang trống.');
                }

                $lines = [];
                $total = 0;

                foreach ($items as $item) {
                    $product = $this->inventory->lock((int) $item['product_id']);
                    $quantity = (int) $item['quantity'];

                    if ($quantity > $product['stock']) {
                        throw new DomainException('Không đủ tồn cho ' . $product['name'] . '.');
                    }

                    $total = Money::addLine($total, $product['price'], $quantity);
                    $lines[] = [$product, $quantity];
                }

                $cipher = OrderContactCipher::fromEnvironment();
                $id = $this->orders->create(
                    $userId,
                    $token,
                    $contact['recipient_name'],
                    $cipher->encrypt($contact['phone']),
                    $cipher->encrypt($contact['address']),
                    Money::toDecimal($total)
                );

                foreach ($lines as [$product, $quantity]) {
                    $this->orders->addItem($id, $product, $quantity);
                    $this->inventory->decrement((int) $product['id'], $quantity);
                }

                $this->cart->clear($cartId);

                return $token;
            });
        } catch (PDOException $exception) {
            if (
                ($exception->errorInfo[1] ?? null) === 1062
                && $this->orders->findOwned($token, $userId) !== null
            ) {
                return $token;
            }

            throw $exception;
        }
    }
}