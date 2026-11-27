<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

final class Permission
{
    public function __construct(private PDO $db)
    {
    }

    public function forUser(int $id): ?array
    {
        $query = $this->db->prepare(
            'SELECT DISTINCT p.code FROM users u
             LEFT JOIN user_has_roles ur ON ur.user_id=u.id AND ur.deleted_at IS NULL
             LEFT JOIN roles r ON r.id=ur.role_id AND r.deleted_at IS NULL
             LEFT JOIN role_has_permissions rp ON rp.role_id=r.id AND rp.deleted_at IS NULL
             LEFT JOIN permissions p ON p.id=rp.permission_id AND p.deleted_at IS NULL
             WHERE u.id=? AND u.deleted_at IS NULL'
        );
        $query->execute([$id]);
        $rows = $query->fetchAll(PDO::FETCH_COLUMN);

        return $rows === [] ? null : array_values(array_filter($rows, 'is_string'));
    }
}