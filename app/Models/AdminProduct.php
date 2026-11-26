<?php

declare(strict_types=1);

namespace app\Models;

use app\Support\Database;
use DomainException;
use PDO;

final class AdminProduct
{
    private const SELECT = 'SELECT p.*, c.name AS category_name, COALESCE(i.quantity, 0) AS stock
        FROM products p JOIN categories c ON c.id = p.category_id
        LEFT JOIN inventory i ON i.product_id = p.id WHERE p.deleted_at IS NULL';

    public function __construct(private PDO $db)
    {
    }

    public function all(): array
    {
        return $this->db->query(self::SELECT . ' ORDER BY p.id DESC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $query = $this->db->prepare(self::SELECT . ' AND p.id = ?');
        $query->execute([$id]);

        return $query->fetch() ?: null;
    }

    public function save(?int $id, array $data): void
    {
        Database::transaction($this->db, function () use ($id, $data): void {
            $values = [
                $data['category_id'],
                $data['sku'],
                strtolower($data['sku']),
                $data['name'],
                $data['description'],
                $data['price'],
                $data['is_active'],
            ];

            $setStock = $id === null || (int) $data['stock'] !== (int) $data['stock_expected'];

            if ($id === null) {
                $query = $this->db->prepare(
                    'INSERT INTO products (category_id, sku, slug, name, description, price, is_active)
                     VALUES (?, ?, ?, ?, ?, ?, ?)'
                );
                $query->execute($values);
                $id = (int) $this->db->lastInsertId();
            } else {
                $query = $this->db->prepare(
                    'SELECT id FROM products WHERE id = ? AND deleted_at IS NULL FOR UPDATE'
                );
                $query->execute([$id]);

                if (!$query->fetchColumn()) {
                    throw new DomainException('Sản phẩm đã bị xóa.');
                }

                if ($setStock) {
                    $stock = $this->db->prepare(
                        'SELECT quantity FROM inventory WHERE product_id = ? FOR UPDATE'
                    );
                    $stock->execute([$id]);

                    if ((int) $stock->fetchColumn() !== (int) $data['stock_expected']) {
                        throw new DomainException(
                            'Tồn kho đã thay đổi. Hãy tải lại trang sửa trước khi nhập tồn mới.'
                        );
                    }
                }

                $query = $this->db->prepare(
                    'UPDATE products SET category_id = ?, sku = ?, slug = ?, name = ?,
                        description = ?, price = ?, is_active = ? WHERE id = ?'
                );
                $query->execute([...$values, $id]);
            }

            if (!$setStock) {
                return;
            }

            $query = $this->db->prepare(
                'INSERT INTO inventory (product_id, quantity) VALUES (?, ?)
                 ON DUPLICATE KEY UPDATE quantity = ?'
            );
            $query->execute([$id, $data['stock'], $data['stock']]);
        });
    }

    public function disable(int $id): void
    {
        $query = $this->db->prepare(
            'UPDATE products SET is_active = 0 WHERE id = ? AND deleted_at IS NULL'
        );
        $query->execute([$id]);
    }
}