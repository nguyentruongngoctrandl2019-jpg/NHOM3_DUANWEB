<?php

declare(strict_types=1);

namespace app\Models;

use PDO;

final class Category
{
    public function __construct(private PDO $db)
    {
    }

    public function all(): array
    {
        return $this->db->query(
            'SELECT id, name, slug FROM categories WHERE deleted_at IS NULL ORDER BY id'
        )->fetchAll();
    }
}