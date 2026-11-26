<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use PDOException;
use Throwable;
use InvalidArgumentException;
use RuntimeException;
use App\Support\Money;

final class AdminProduct
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lay danh sach san pham quan tri.
     */
    public function all(string $keyword = ''): array
    {
        $sql = "
            SELECT
                p.*,
                c.name AS category_name,
                COALESCE(i.quantity, 0) AS stock
            FROM products p
            INNER JOIN categories c
                ON c.id = p.category_id
            LEFT JOIN inventory i
                ON i.product_id = p.id
                AND i.deleted_at IS NULL
            WHERE p.deleted_at IS NULL
        ";

        $params = [];

        $keyword = trim($keyword);

        if ($keyword !== '') {
            $sql .= "
                AND (
                    p.name LIKE :name
                    OR p.sku LIKE :sku
                )
            ";

            $params['name'] = '%' . $keyword . '%';
            $params['sku'] = '%' . $keyword . '%';
        }

        $sql .= " ORDER BY p.id DESC";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tim san pham theo ID.
     */
    public function find(int $id): ?array
    {
        $sql = "
            SELECT
                p.*,
                COALESCE(i.quantity, 0) AS stock
            FROM products p
            LEFT JOIN inventory i
                ON i.product_id = p.id
                AND i.deleted_at IS NULL
            WHERE p.id = :id
                AND p.deleted_at IS NULL
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute(['id' => $id]);

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        return $product === false
            ? null
            : $product;
    }

    /**
     * Lay danh muc san pham.
     */
    public function categories(): array
    {
        $sql = "
            SELECT id, name
            FROM categories
            WHERE deleted_at IS NULL
            ORDER BY name ASC
        ";

        return $this->pdo
            ->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Kiem tra du lieu dau vao.
     */
    private function validate(array $data): array
    {
        foreach (
            [
                'sku',
                'name',
                'category_id',
                'price',
                'warranty_months',
                'is_active',
                'stock',
                'description'
            ] as $key
        ) {
            if (
                isset($data[$key])
                && !is_scalar($data[$key])
            ) {
                throw new InvalidArgumentException(
                    'Du lieu khong hop le: ' . $key
                );
            }
        }

        $sku = trim((string) ($data['sku'] ?? ''));

        $name = trim((string) ($data['name'] ?? ''));

        $description = trim(
            (string) ($data['description'] ?? '')
        );

        $price = trim(
            (string) ($data['price'] ?? '')
        );

        $categoryId = filter_var(
            $data['category_id'] ?? '',
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        $warranty = filter_var(
            $data['warranty_months'] ?? 0,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 0,
                    'max_range' => 65535
                ]
            ]
        );

        $stock = filter_var(
            $data['stock'] ?? 0,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 0,
                    'max_range' => 2147483647
                ]
            ]
        );

        $active = (string) (
            $data['is_active'] ?? '1'
        );

        // Validate SKU

        if (!preg_match(
            '/^[A-Za-z0-9_-]{1,60}$/D',
            $sku
        )) {
            throw new InvalidArgumentException(
                'SKU khong hop le'
            );
        }

        // Validate ten san pham

        if (
            $name === ''
            || preg_match('//u', $name) !== 1
            || preg_match('/^.{1,180}$/usD', $name) !== 1
        ) {
            throw new InvalidArgumentException(
                'Ten san pham tu 1 den 180 ky tu'
            );
        }

        // Validate danh muc

        if ($categoryId === false) {
            throw new InvalidArgumentException(
                'Danh muc khong hop le'
            );
        }

        $stmt = $this->pdo->prepare("
            SELECT id
            FROM categories
            WHERE id = :id
                AND deleted_at IS NULL
        ");

        $stmt->execute(['id' => $categoryId]);

        if (!$stmt->fetchColumn()) {
            throw new InvalidArgumentException(
                'Danh muc khong ton tai'
            );
        }

        // Validate gia DECIMAL(15,2)

        if (!preg_match(
            '/^\d{1,13}(\.\d{1,2})?$/D',
            $price
        )) {
            throw new InvalidArgumentException(
                'Gia san pham khong hop le'
            );
        }

        $priceMinor = Money::fromDecimal($price);

        if ($priceMinor < 0) {
            throw new InvalidArgumentException(
                'Gia khong duoc am'
            );
        }

        if ($warranty === false) {
            throw new InvalidArgumentException(
                'Thoi gian bao hanh khong hop le'
            );
        }

        if ($stock === false) {
            throw new InvalidArgumentException(
                'So luong ton kho khong hop le'
            );
        }

        if (!in_array($active, ['0', '1'], true)) {
            throw new InvalidArgumentException(
                'Trang thai san pham khong hop le'
            );
        }

        return [
            'sku' => strtoupper($sku),
            'name' => $name,
            'category_id' => $categoryId,
            'description' => $description,
            'price' => Money::toDecimal($priceMinor),
            'warranty_months' => $warranty,
            'is_active' => (int) $active,
            'stock' => $stock
        ];
    }

    /**
     * Tao slug duy nhat khi them san pham.
     */
    private function makeSlug(string $sku): string
    {
        return strtolower($sku)
            . '-'
            . bin2hex(random_bytes(8));
    }

    /**
     * Them san pham moi.
     */
    public function create(
        array $data,
        ?int $userId = null
    ): int {

        $data = $this->validate($data);

        $this->pdo->beginTransaction();

        try {

            $sql = "
                INSERT INTO products (
                    category_id,
                    sku,
                    slug,
                    name,
                    description,
                    price,
                    warranty_months,
                    is_active,
                    created_by,
                    updated_by
                ) VALUES (
                    :category_id,
                    :sku,
                    :slug,
                    :name,
                    :description,
                    :price,
                    :warranty_months,
                    :is_active,
                    :created_by,
                    :updated_by
                )
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                'category_id' => $data['category_id'],
                'sku' => $data['sku'],
                'slug' => $this->makeSlug($data['sku']),
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'warranty_months' =>
                $data['warranty_months'],
                'is_active' => $data['is_active'],
                'created_by' => $userId,
                'updated_by' => $userId
            ]);

            $productId = (int) $this->pdo
                ->lastInsertId();

            // Tao ton kho ban dau

            $stmt = $this->pdo->prepare("
                INSERT INTO inventory (
                    product_id,
                    quantity,
                    created_by,
                    updated_by
                ) VALUES (
                    :product_id,
                    :quantity,
                    :created_by,
                    :updated_by
                )
            ");

            $stmt->execute([
                'product_id' => $productId,
                'quantity' => $data['stock'],
                'created_by' => $userId,
                'updated_by' => $userId
            ]);

            // Ghi lich su nhap kho

            if ($data['stock'] > 0) {

                $this->recordMovement(
                    $productId,
                    $data['stock'],
                    'receipt',
                    'Nhap kho ban dau',
                    $userId
                );
            }

            $this->pdo->commit();

            return $productId;
        } catch (Throwable $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            $this->handleException($e);
        }
    }

    /**
     * Cap nhat san pham.
     */
    public function update(
        int $id,
        array $data,
        ?int $userId = null
    ): void {

        $data = $this->validate($data);

        $this->pdo->beginTransaction();

        try {

            // Khoa product truoc inventory

            $stmt = $this->pdo->prepare("
                SELECT id
                FROM products
                WHERE id = :id
                    AND deleted_at IS NULL
                FOR UPDATE
            ");

            $stmt->execute(['id' => $id]);

            if (!$stmt->fetchColumn()) {
                throw new RuntimeException(
                    'San pham khong ton tai'
                );
            }

            // Khoa inventory

            $stmt = $this->pdo->prepare("
                SELECT quantity, deleted_at
                FROM inventory
                WHERE product_id = :id
                FOR UPDATE
            ");

            $stmt->execute(['id' => $id]);

            $inventory = $stmt->fetch(
                PDO::FETCH_ASSOC
            );

            if (
                $inventory === false
                || $inventory['deleted_at'] !== null
            ) {
                throw new RuntimeException(
                    'Du lieu ton kho khong hop le'
                );
            }

            $oldStock = (int) $inventory['quantity'];

            // Kiem tra form co bi cu hay khong

            if (isset($data['stock'])) {
                // So luong kho se duoc xu ly ben duoi.
            }

            $sql = "
                UPDATE products
                SET
                    category_id = :category_id,
                    sku = :sku,
                    name = :name,
                    description = :description,
                    price = :price,
                    warranty_months = :warranty_months,
                    is_active = :is_active,
                    updated_by = :updated_by
                WHERE id = :id
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                'category_id' => $data['category_id'],
                'sku' => $data['sku'],
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'warranty_months' =>
                $data['warranty_months'],
                'is_active' => $data['is_active'],
                'updated_by' => $userId,
                'id' => $id
            ]);

            // Khong ghi de ton kho khi sua thong tin.
            // Ton kho duoc dieu chinh bang adjustStock().

            $this->pdo->commit();
        } catch (Throwable $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            $this->handleException($e);
        }
    }

    /**
     * Dieu chinh ton kho theo so luong tang/giam.
     */
    public function adjustStock(
        int $id,
        int $change,
        ?int $userId = null
    ): void {

        if (
            $change === 0
            || $change < -2147483648
            || $change > 2147483647
        ) {
            throw new InvalidArgumentException(
                'So luong dieu chinh khong hop le'
            );
        }

        $this->pdo->beginTransaction();

        try {

            // Khoa product

            $stmt = $this->pdo->prepare("
                SELECT id
                FROM products
                WHERE id = :id
                    AND deleted_at IS NULL
                FOR UPDATE
            ");

            $stmt->execute(['id' => $id]);

            if (!$stmt->fetchColumn()) {
                throw new RuntimeException(
                    'San pham khong ton tai'
                );
            }

            // Khoa inventory

            $stmt = $this->pdo->prepare("
                SELECT quantity
                FROM inventory
                WHERE product_id = :id
                    AND deleted_at IS NULL
                FOR UPDATE
            ");

            $stmt->execute(['id' => $id]);

            $quantity = $stmt->fetchColumn();

            if ($quantity === false) {
                throw new RuntimeException(
                    'Khong tim thay ton kho'
                );
            }

            $newStock = (int) $quantity + $change;

            if (
                $newStock < 0
                || $newStock > 2147483647
            ) {
                throw new InvalidArgumentException(
                    'Ton kho vuot gioi han cho phep'
                );
            }

            $stmt = $this->pdo->prepare("
                UPDATE inventory
                SET quantity = :quantity,
                    updated_by = :updated_by
                WHERE product_id = :id
            ");

            $stmt->execute([
                'quantity' => $newStock,
                'updated_by' => $userId,
                'id' => $id
            ]);

            $this->recordMovement(
                $id,
                $change,
                'adjustment',
                'Quan tri dieu chinh ton kho',
                $userId
            );

            $this->pdo->commit();
        } catch (Throwable $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            $this->handleException($e);
        }
    }

    /**
     * Ngung kinh doanh san pham.
     */
    public function disable(
        int $id,
        ?int $userId = null
    ): bool {

        $stmt = $this->pdo->prepare("
            UPDATE products
            SET
                is_active = 0,
                updated_by = :updated_by
            WHERE id = :id
                AND deleted_at IS NULL
                AND is_active = 1
        ");

        $stmt->execute([
            'id' => $id,
            'updated_by' => $userId
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Ghi lich su thay doi ton kho.
     */
    private function recordMovement(
        int $productId,
        int $change,
        string $type,
        string $reason,
        ?int $userId
    ): void {

        $sql = "
            INSERT INTO stock_movements (
                product_id,
                order_id,
                movement_type,
                quantity_change,
                reason,
                created_by,
                updated_by
            ) VALUES (
                :product_id,
                NULL,
                :movement_type,
                :quantity_change,
                :reason,
                :created_by,
                :updated_by
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'product_id' => $productId,
            'movement_type' => $type,
            'quantity_change' => $change,
            'reason' => $reason,
            'created_by' => $userId,
            'updated_by' => $userId
        ]);
    }

    /**
     * Xu ly loi database.
     */
    private function handleException(
        Throwable $e
    ): never {

        if (
            $e instanceof PDOException
            && (int) ($e->errorInfo[1] ?? 0) === 1062
        ) {
            throw new InvalidArgumentException(
                'SKU hoac slug da ton tai',
                0,
                $e
            );
        }

        throw $e;
    }
}
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
