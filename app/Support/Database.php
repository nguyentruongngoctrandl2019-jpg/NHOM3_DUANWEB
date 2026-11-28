<?php

declare(strict_types=1);

namespace App\Support;

use PDO;
use PDOException;
use RuntimeException;
use Throwable;

final class Database
{
    private static ?PDO $connection = null;

    public static function connect(
        ?array $config = null
    ): PDO {

        if ($config === null) {

            if (self::$connection !== null) {
                return self::$connection;
            }

            $config = require
                __DIR__ . '/../../config/database.php';
        }

        $host = $config['host'] ?? '127.0.0.1';

        $port = (int) ($config['port'] ?? 3306);

        $name = $config['name']
            ?? $config['dbname']
            ?? '';

        $user = $config['user']
            ?? $config['username']
            ?? 'root';

        $password = $config['password'] ?? '';

        $charset = $config['charset'] ?? 'utf8mb4';

        if ($name === '') {
            throw new RuntimeException(
                'Chưa cấu hình tên Database.'
            );
        }

        $dsn = "mysql:host={$host};"
             . "port={$port};"
             . "dbname={$name};"
             . "charset={$charset}";

        $db = new PDO(
            $dsn,
            $user,
            $password,
            [
                PDO::ATTR_ERRMODE =>
                    PDO::ERRMODE_EXCEPTION,

                PDO::ATTR_DEFAULT_FETCH_MODE =>
                    PDO::FETCH_ASSOC,

                PDO::ATTR_EMULATE_PREPARES =>
                    false,
            ]
        );

        $db->exec(
            'SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED'
        );

        self::$connection = $db;

        return $db;
    }

    public static function transaction(
        PDO $db,
        callable $work
    ): mixed {

        if ($db->inTransaction()) {
            throw new RuntimeException(
                'Transaction đang được thực hiện.'
            );
        }

        for ($attempt = 1; $attempt <= 3; $attempt++) {

            $db->beginTransaction();

            try {

                $result = $work();

                $db->commit();

                return $result;

            } catch (Throwable $error) {

                if ($db->inTransaction()) {
                    $db->rollBack();
                }

                $deadlock =
                    $error instanceof PDOException
                    && (int) (
                        $error->errorInfo[1] ?? 0
                    ) === 1213;

                if (!$deadlock || $attempt === 3) {
                    throw $error;
                }

                usleep(10000 * $attempt);
            }
        }

        throw new RuntimeException(
            'Không thể hoàn thành Transaction.'
        );
    }
}