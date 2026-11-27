<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

final class Product
{
    private const FROM = ' FROM products p JOIN categories c ON c.id=p.category_id AND c.deleted_at IS NULL
        LEFT JOIN inventory i ON i.product_id=p.id WHERE p.is_active=1 AND p.deleted_at IS NULL';

    private const SELECT = 'SELECT p.*, c.name AS category_name, c.slug AS category_slug, COALESCE(i.quantity,0) AS stock';

    public function __construct(private PDO $db)
    {
    }

    public function search(array $filters): array
    {
        $from = self::FROM;
        $params = [];

        if ($filters['q'] !== '') {
            $prefix = '%' . strtr($filters['q'], ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
            $from .= " AND (p.name LIKE ? ESCAPE '!' OR p.sku LIKE ? ESCAPE '!')";
            $params = [$prefix, $prefix];
        }

        if ($filters['category'] !== null) {
            $from .= ' AND p.category_id=?';
            $params[] = $filters['category'];
        }

        $count = $this->db->prepare('SELECT COUNT(*)' . $from);
        $count->execute($params);
        $total = (int) $count->fetchColumn();
        $limit = 6;
        $pages = max(1, (int) ceil($total / $limit));
        $page = min($filters['page'], $pages);
        $offset = ($page - 1) * $limit;

        $query = $this->db->prepare(self::SELECT . $from . " ORDER BY p.id LIMIT $limit OFFSET $offset");
        $query->execute($params);
        $products = array_map(self::image(...), $query->fetchAll());

        return ['products' => $products, 'pagination' => compact('total', 'page', 'pages')];
    }

    public function findActive(int $id): ?array
    {
        $query = $this->db->prepare(self::SELECT . self::FROM . ' AND p.id=?');
        $query->execute([$id]);
        $product = $query->fetch();

        return $product ? self::image($product) : null;
    }

    private static function image(array $product): array
    {
        $slug = $product['category_slug'];
        $image = in_array($slug, ['tivi', 'tu-lanh', 'may-giat', 'dieu-hoa', 'gia-dung'], true)
            ? $slug
            : 'placeholder';

        $product['image_url'] = '/assets/images/' . $image . '.webp';

        return $product;
    }
}