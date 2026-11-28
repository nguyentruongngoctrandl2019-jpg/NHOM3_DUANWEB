<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Database;
use App\Support\Money;

use PDO;
use PDOException;
use Throwable;
use DomainException;
use InvalidArgumentException;

final class AdminProduct
{
    private PDO $pdo;

    private const MAX_STOCK = 2147483647;

    private const SELECT = "
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

    // ==========================================
    // 1. KHOI TAO MODEL
    // ==========================================

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // ==========================================
    // 2. DANH SACH SAN PHAM
    // ==========================================

    public function all(string $keyword = ''): array
    {
        $sql = self::SELECT;

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

        $sql .= ' ORDER BY p.id DESC';

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==========================================
    // 3. TIM SAN PHAM THEO ID
    // ==========================================

    public function find(int $id): ?array
    {
        $sql = self::SELECT . "
            AND p.id = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'id' => $id
        ]);

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        return $product === false
            ? null
            : $product;
    }

    // ==========================================
    // 4. DANH SACH DANH MUC
    // ==========================================

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

    // ==========================================
    // 5. KIEM TRA DU LIEU DAU VAO
    // ==========================================

    private function validate(array $data): array
    {
        $fields = [
            'sku',
            'name',
            'category_id',
            'price',
            'warranty_months',
            'is_active',
            'stock',
            'stock_expected',
            'description',
        ];

        foreach ($fields as $field) {

            if (
                isset($data[$field])
                && !is_scalar($data[$field])
            ) {
                throw new InvalidArgumentException(
                    'Dữ liệu không hợp lệ: ' . $field
                );
            }
        }

        $sku = trim(
            (string) ($data['sku'] ?? '')
        );

        $name = trim(
            (string) ($data['name'] ?? '')
        );

        $description = trim(
            (string) ($data['description'] ?? '')
        );

        $price = trim(
            (string) ($data['price'] ?? '')
        );

        $categoryId = filter_var(
            $data['category_id'] ?? '',
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1
                ]
            ]
        );

        $warranty = filter_var(
            $data['warranty_months'] ?? 0,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 0,
                    'max_range' => 65535,
                ]
            ]
        );

        $stock = filter_var(
            $data['stock'] ?? 0,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 0,
                    'max_range' => self::MAX_STOCK,
                ]
            ]
        );

        $active = (string) (
            $data['is_active'] ?? '1'
        );

        // Kiem tra SKU.

        if (!preg_match(
            '/\A[A-Za-z0-9_-]{1,60}\z/',
            $sku
        )) {
            throw new InvalidArgumentException(
                'Mã SKU không hợp lệ.'
            );
        }

        // Kiem tra ten san pham.

        if (
            $name === ''
            || preg_match(
                '/\A.{1,180}\z/us',
                $name
            ) !== 1
        ) {
            throw new InvalidArgumentException(
                'Tên sản phẩm phải từ 1 đến 180 ký tự.'
            );
        }

        // Kiem tra danh muc.

        if ($categoryId === false) {

            throw new InvalidArgumentException(
                'Danh mục không hợp lệ.'
            );
        }

        $stmt = $this->pdo->prepare("
            SELECT id
            FROM categories
            WHERE id = :id
                AND deleted_at IS NULL
        ");

        $stmt->execute([
            'id' => $categoryId
        ]);

        if (!$stmt->fetchColumn()) {

            throw new InvalidArgumentException(
                'Danh mục không tồn tại.'
            );
        }

        // Kiem tra gia tien.

        if (!preg_match(
            '/\A\d{1,13}(?:\.\d{1,2})?\z/',
            $price
        )) {
            throw new InvalidArgumentException(
                'Giá sản phẩm không hợp lệ.'
            );
        }

        $priceMinor = Money::fromDecimal($price);

        if ($priceMinor < 0) {

            throw new InvalidArgumentException(
                'Giá sản phẩm không được âm.'
            );
        }

        // Kiem tra bao hanh.

        if ($warranty === false) {

            throw new InvalidArgumentException(
                'Thời gian bảo hành không hợp lệ.'
            );
        }

        // Kiem tra ton kho.

        if ($stock === false) {

            throw new InvalidArgumentException(
                'Số lượng tồn kho không hợp lệ.'
            );
        }

        // Kiem tra trang thai.

        if (!in_array(
            $active,
            ['0', '1'],
            true
        )) {
            throw new InvalidArgumentException(
                'Trạng thái sản phẩm không hợp lệ.'
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
            'stock' => $stock,
        ];
    }

    // ==========================================
    // 6. TAO SLUG SAN PHAM
    // ==========================================

    private function makeSlug(string $sku): string
    {
        return strtolower($sku)
            . '-'
            . bin2hex(random_bytes(8));
    }

    // ==========================================
    // 7. THEM SAN PHAM MOI
    // ==========================================

    public function create(
        array $data,
        ?int $userId = null
    ): int {

        $data = $this->validate($data);

        try {

            return Database::transaction(
                $this->pdo,
                function () use (
                    $data,
                    $userId
                ): int {

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
                        'category_id' =>
                            $data['category_id'],

                        'sku' => $data['sku'],

                        'slug' =>
                            $this->makeSlug($data['sku']),

                        'name' => $data['name'],

                        'description' =>
                            $data['description'],

                        'price' => $data['price'],

                        'warranty_months' =>
                            $data['warranty_months'],

                        'is_active' =>
                            $data['is_active'],

                        'created_by' => $userId,

                        'updated_by' => $userId,
                    ]);

                    $productId = (int) $this->pdo
                        ->lastInsertId();

                    // Tao ton kho ban dau.

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
                        'updated_by' => $userId,
                    ]);

                    // Ghi lich su nhap kho.

                    if ($data['stock'] > 0) {

                        $this->recordMovement(
                            $productId,
                            $data['stock'],
                            'receipt',
                            'Nhập kho ban đầu',
                            $userId
                        );
                    }

                    return $productId;
                }
            );

        } catch (Throwable $error) {

            $this->handleException($error);
        }
    }

    // ==========================================
    // 8. CAP NHAT SAN PHAM
    // ==========================================

    public function update(
        int $id,
        array $data,
        ?int $userId = null
    ): void {

        $current = $this->find($id);

        if ($current === null) {

            throw new DomainException(
                'Sản phẩm không tồn tại.'
            );
        }

        // Giu thoi gian bao hanh neu form khong gui.

        $data['warranty_months'] =
            $data['warranty_months']
            ?? $current['warranty_months']
            ?? 0;

        $data = $this->validate($data);

        try {

            Database::transaction(
                $this->pdo,
                function () use (
                    $id,
                    $data,
                    $userId
                ): void {

                    $this->lockProduct($id);

                    $this->updateProduct(
                        $id,
                        $data,
                        $userId
                    );
                }
            );

        } catch (Throwable $error) {

            $this->handleException($error);
        }
    }

    // ==========================================
    // 9. LUU SAN PHAM
    // ==========================================

    public function save(
        ?int $id,
        array $data,
        ?int $userId = null
    ): void {

        // Them san pham moi.

        if ($id === null) {

            $this->create($data, $userId);

            return;
        }

        // Cap nhat san pham.

        $current = $this->find($id);

        if ($current === null) {

            throw new DomainException(
                'Sản phẩm không tồn tại.'
            );
        }

        $data['warranty_months'] =
            $data['warranty_months']
            ?? $current['warranty_months']
            ?? 0;

        $stockProvided = array_key_exists(
            'stock',
            $data
        );

        $stockExpected = null;

        if (isset($data['stock_expected'])) {

            $stockExpected = filter_var(
                $data['stock_expected'],
                FILTER_VALIDATE_INT,
                [
                    'options' => [
                        'min_range' => 0,
                        'max_range' => self::MAX_STOCK,
                    ]
                ]
            );

            if ($stockExpected === false) {

                throw new InvalidArgumentException(
                    'Tồn kho ban đầu không hợp lệ.'
                );
            }
        }

        $validated = $this->validate($data);

        try {

            Database::transaction(
                $this->pdo,
                function () use (
                    $id,
                    $validated,
                    $stockProvided,
                    $stockExpected,
                    $userId
                ): void {

                    $this->lockProduct($id);

                    $this->updateProduct(
                        $id,
                        $validated,
                        $userId
                    );

                    // Khong thay doi kho neu form
                    // khong gui so luong ton kho.

                    if (!$stockProvided) {
                        return;
                    }

                    // Khong ghi de ton kho khi thieu
                    // gia tri ton kho ban dau.

                    if ($stockExpected === null) {
                        return;
                    }

                    $newStock = $validated['stock'];

                    if ($newStock === $stockExpected) {
                        return;
                    }

                    $currentStock = $this->lockStock($id);

                    if ($currentStock !== $stockExpected) {

                        throw new DomainException(
                            'Tồn kho đã thay đổi. '
                            . 'Hãy tải lại trang trước khi cập nhật.'
                        );
                    }

                    $change = $newStock - $currentStock;

                    $this->writeStock(
                        $id,
                        $newStock,
                        $userId
                    );

                    $this->recordMovement(
                        $id,
                        $change,
                        'adjustment',
                        'Cập nhật tồn kho sản phẩm',
                        $userId
                    );
                }
            );

        } catch (Throwable $error) {

            $this->handleException($error);
        }
    }

    // ==========================================
    // 10. DIEU CHINH TON KHO
    // ==========================================

    public function adjustStock(
        int $id,
        int $change,
        ?int $userId = null
    ): void {

        if (
            $change === 0
            || $change < -2147483648
            || $change > self::MAX_STOCK
        ) {
            throw new InvalidArgumentException(
                'Số lượng điều chỉnh không hợp lệ.'
            );
        }

        try {

            Database::transaction(
                $this->pdo,
                function () use (
                    $id,
                    $change,
                    $userId
                ): void {

                    $this->lockProduct($id);

                    $currentStock = $this->lockStock($id);

                    $newStock = $currentStock + $change;

                    if (
                        $newStock < 0
                        || $newStock > self::MAX_STOCK
                    ) {
                        throw new InvalidArgumentException(
                            'Tồn kho vượt giới hạn cho phép.'
                        );
                    }

                    $this->writeStock(
                        $id,
                        $newStock,
                        $userId
                    );

                    $this->recordMovement(
                        $id,
                        $change,
                        'adjustment',
                        'Quản trị điều chỉnh tồn kho',
                        $userId
                    );
                }
            );

        } catch (Throwable $error) {

            $this->handleException($error);
        }
    }

    // ==========================================
    // 11. NGUNG KINH DOANH SAN PHAM
    // ==========================================

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
            'updated_by' => $userId,
        ]);

        return $stmt->rowCount() > 0;
    }

    // ==========================================
    // 12. KHOA SAN PHAM
    // ==========================================

    private function lockProduct(int $id): void
    {
        $stmt = $this->pdo->prepare("
            SELECT id
            FROM products
            WHERE id = :id
                AND deleted_at IS NULL
            FOR UPDATE
        ");

        $stmt->execute([
            'id' => $id
        ]);

        if (!$stmt->fetchColumn()) {

            throw new DomainException(
                'Sản phẩm không tồn tại.'
            );
        }
    }

    // ==========================================
    // 13. KHOA TON KHO
    // ==========================================

    private function lockStock(int $id): int
    {
        $stmt = $this->pdo->prepare("
            SELECT quantity
            FROM inventory
            WHERE product_id = :id
                AND deleted_at IS NULL
            FOR UPDATE
        ");

        $stmt->execute([
            'id' => $id
        ]);

        $quantity = $stmt->fetchColumn();

        if ($quantity === false) {

            throw new DomainException(
                'Không tìm thấy dữ liệu tồn kho.'
            );
        }

        return (int) $quantity;
    }

    // ==========================================
    // 14. CAP NHAT THONG TIN SAN PHAM
    // ==========================================

    private function updateProduct(
        int $id,
        array $data,
        ?int $userId
    ): void {

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
                AND deleted_at IS NULL
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

            'id' => $id,
        ]);
    }

    // ==========================================
    // 15. CAP NHAT SO LUONG TON KHO
    // ==========================================

    private function writeStock(
        int $id,
        int $quantity,
        ?int $userId
    ): void {

        $stmt = $this->pdo->prepare("
            UPDATE inventory
            SET
                quantity = :quantity,
                updated_by = :updated_by
            WHERE product_id = :id
                AND deleted_at IS NULL
        ");

        $stmt->execute([
            'quantity' => $quantity,
            'updated_by' => $userId,
            'id' => $id,
        ]);
    }

    // ==========================================
    // 16. GHI LICH SU THAY DOI TON KHO
    // ==========================================

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
            'updated_by' => $userId,
        ]);
    }

    // ==========================================
    // 17. XU LY LOI DATABASE
    // ==========================================

    private function handleException(
        Throwable $error
    ): never {

        if (
            $error instanceof PDOException
            && (int) (
                $error->errorInfo[1] ?? 0
            ) === 1062
        ) {

            throw new InvalidArgumentException(
                'Dữ liệu bị trùng. Vui lòng kiểm tra SKU.',
                0,
                $error
            );
        }

        throw $error;
    }
}