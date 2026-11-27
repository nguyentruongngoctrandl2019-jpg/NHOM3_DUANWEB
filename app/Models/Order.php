<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

final class Order
{
    public const LABELS = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'shipped' => 'Đang giao',
        'completed' => 'Hoàn tất',
        'cancelled' => 'Đã hủy',
    ];

    public const TRANSITIONS = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['shipped', 'cancelled'],
        'shipped' => ['completed'],
        'completed' => [],
        'cancelled' => [],
    ];

    public function __construct(private PDO $db)
    {
    }

    public function findOwned(string $publicId, int $userId): ?array
    {
        $query = $this->db->prepare('SELECT * FROM orders WHERE public_id = ? AND user_id = ?');
        $query->execute([$publicId, $userId]);

        return $query->fetch() ?: null;
    }

    public function list(?int $userId = null): array
    {
        $sql = 'SELECT id, public_id, status, recipient_name, grand_total, created_at FROM orders';
        $query = $this->db->prepare(
            $sql . ($userId === null ? '' : ' WHERE user_id = ?') . ' ORDER BY id DESC'
        );
        $query->execute($userId === null ? [] : [$userId]);

        return $query->fetchAll();
    }

    public function items(int $id): array
    {
        $query = $this->db->prepare(
            'SELECT product_id, product_name, quantity, unit_price
             FROM order_items WHERE order_id = ? ORDER BY product_id'
        );
        $query->execute([$id]);

        return $query->fetchAll();
    }

    public function create(
        int $userId,
        string $publicId,
        string $name,
        string $phone,
        string $address,
        string $total
    ): int {
        $query = $this->db->prepare(
            "INSERT INTO orders (user_id, public_id, status, recipient_name, phone_encrypted,
                address_encrypted, payment_method, shipping_fee, grand_total)
             VALUES (?, ?, 'pending', ?, ?, ?, 'cod', 0.00, ?)"
        );
        $query->execute([$userId, $publicId, $name, $phone, $address, $total]);

        return (int) $this->db->lastInsertId();
    }

    public function addItem(int $id, array $product, int $quantity): void
    {
        $query = $this->db->prepare(
            'INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price)
             VALUES (?, ?, ?, ?, ?)'
        );
        $query->execute([$id, $product['id'], $product['name'], $quantity, $product['price']]);
    }

    public function transition(string $publicId, string $old, string $new): bool
    {
        $query = $this->db->prepare(
            'UPDATE orders SET status = ? WHERE public_id = ? AND status = ?'
        );
        $query->execute([$new, $publicId, $old]);

        return $query->rowCount() === 1;
    }

    public function restoreStock(string $publicId): void
    {
        $query = $this->db->prepare('SELECT id FROM orders WHERE public_id = ?');
        $query->execute([$publicId]);
        $items = $this->items((int) $query->fetchColumn());

        $lock = $this->db->prepare('SELECT id FROM products WHERE id = ? FOR UPDATE');
        $restore = $this->db->prepare(
            'INSERT INTO inventory (product_id, quantity) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE quantity = quantity + ?'
        );

        foreach ($items as $item) {
            $lock->execute([$item['product_id']]);
            $lock->fetchColumn();
            $restore->execute([$item['product_id'], $item['quantity'], $item['quantity']]);
        }
    }
}