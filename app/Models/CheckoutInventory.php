<?php

declare(strict_types=1);

namespace app\Models;

use DomainException;
use PDO;

final class CheckoutInventory
{
    public function __construct(private PDO $db)
    {
    }

    public function lock(int $productId): array
    {
        $query = $this->db->prepare(
            'SELECT id, name, price, is_active, deleted_at, category_id
             FROM products WHERE id = ? FOR UPDATE'
        );
        $query->execute([$productId]);
        $product = $query->fetch();

        if (!$product || !$product['is_active'] || $product['deleted_at'] !== null) {
            throw new DomainException('Sản phẩm đã ngừng bán hoặc không còn tồn tại.');
        }

        $category = $this->db->prepare(
            'SELECT id FROM categories WHERE id = ? AND deleted_at IS NULL'
        );
        $category->execute([$product['category_id']]);

        if (!$category->fetchColumn()) {
            throw new DomainException('Danh mục của sản phẩm đã ngừng bán.');
        }

        $stock = $this->db->prepare(
            'SELECT quantity FROM inventory WHERE product_id = ? FOR UPDATE'
        );
        $stock->execute([$productId]);
        $product['stock'] = (int) $stock->fetchColumn();

        return $product;
    }

    public function decrement(int $productId, int $quantity): void
    {
        $query = $this->db->prepare(
            'UPDATE inventory SET quantity = quantity - ?
             WHERE product_id = ? AND quantity >= ?'
        );
        $query->execute([$quantity, $productId, $quantity]);

        if ($query->rowCount() !== 1) {
            throw new DomainException('Tồn kho đã thay đổi. Vui lòng kiểm tra lại giỏ hàng.');
        }
    }
}