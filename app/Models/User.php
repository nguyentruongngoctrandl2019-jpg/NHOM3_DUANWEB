<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Database;
use PDO;
use RuntimeException;

final class User
{
    public function __construct(private PDO $db)
    {
    }

    public function findByEmail(string $email): ?array
    {
        $query = $this->db->prepare(
            'SELECT id, password_hash FROM users WHERE email = ? AND deleted_at IS NULL'
        );
        $query->execute([$email]);

        return $query->fetch() ?: null;
    }

    public function register(string $name, string $email, string $password): void
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        Database::transaction($this->db, function () use ($name, $email, $hash): void {
            $query = $this->db->prepare(
                'INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)'
            );
            $query->execute([$name, $email, $hash]);

            $id = $this->db->lastInsertId();
            $query = $this->db->prepare(
                "INSERT INTO user_has_roles (user_id, role_id)
                 SELECT ?, id FROM roles WHERE code = 'customer' AND deleted_at IS NULL"
            );
            $query->execute([$id]);

            if ($query->rowCount() !== 1) {
                throw new RuntimeException('Missing customer role');
            }
        });
    }
}