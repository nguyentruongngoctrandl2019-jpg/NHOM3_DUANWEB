<?php

declare(strict_types=1);

namespace app\Models;

use PDO;
use RuntimeException;

final class Cart
{
    public function __construct(private PDO $db)
    {
    }

    /** Gọi trong transaction trước khi ghi giỏ hoặc tạo đơn. */
    public function lock(int $userId): int
    {
        if (!$this->db->inTransaction()) {
            throw new RuntimeException('Cart lock requires a transaction');
        }

        $create = $this->db->prepare(
            'INSERT INTO carts (user_id) VALUES (?) ON DUPLICATE KEY UPDATE user_id=user_id'
        );
        $create->execute([$userId]);

        $lock = $this->db->prepare('SELECT id FROM carts WHERE user_id=? FOR UPDATE');
        $lock->execute([$userId]);

        return (int) $lock->fetchColumn();
    }

    public function activeItems(int $userId): array
    {
        $query = $this->db->prepare(
            'SELECT ci.product_id, ci.quantity, p.name, p.price,
                COALESCE(i.quantity,0) AS stock,
                (p.is_active=1 AND p.deleted_at IS NULL AND c.deleted_at IS NULL) AS available
             FROM carts b JOIN cart_items ci ON ci.cart_id=b.id AND ci.deleted_at IS NULL
             JOIN products p ON p.id=ci.product_id JOIN categories c ON c.id=p.category_id
             LEFT JOIN inventory i ON i.product_id=p.id
             WHERE b.user_id=? ORDER BY ci.product_id'
        );
        $query->execute([$userId]);

        return $query->fetchAll();
    }

    public function quantity(int $cartId, int $productId): ?int
    {
        $query = $this->db->prepare(
            'SELECT quantity FROM cart_items
             WHERE cart_id=? AND product_id=? AND deleted_at IS NULL FOR UPDATE'
        );
        $query->execute([$cartId, $productId]);
        $quantity = $query->fetchColumn();

        return $quantity === false ? null : (int) $quantity;
    }

    public function save(int $cartId, int $productId, int $quantity): void
    {
        $query = $this->db->prepare(
            'INSERT INTO cart_items (cart_id,product_id,quantity) VALUES (?,?,?)
             ON DUPLICATE KEY UPDATE quantity=?, deleted_at=NULL'
        );
        $query->execute([$cartId, $productId, $quantity, $quantity]);
    }

    public function remove(int $cartId, int $productId): void
    {
        $query = $this->db->prepare(
            'UPDATE cart_items SET deleted_at=CURRENT_TIMESTAMP
             WHERE cart_id=? AND product_id=? AND deleted_at IS NULL'
        );
        $query->execute([$cartId, $productId]);
    }

    public function clear(int $cartId): void
    {
        $query = $this->db->prepare(
            'UPDATE cart_items SET deleted_at=CURRENT_TIMESTAMP WHERE cart_id=? AND deleted_at IS NULL'
        );
        $query->execute([$cartId]);
    }
}