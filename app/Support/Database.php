<?php

namespace App\Support;

use PDO;
use RuntimeException;

class Database
{
    private static ?PDO $connection = null;

    public static function connect(): PDO
    {
        if (self::$connection === null) {

            $config = require __DIR__
                . '/../../config/database.php';

            if ($config['password'] === '') {
                throw new RuntimeException(
                    'Chua nhap mat khau MySQL'
                );
            }

            $dsn = "mysql:host={$config['host']};"
                 . "port={$config['port']};"
                 . "dbname={$config['dbname']};"
                 . "charset={$config['charset']}";

            self::$connection = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE =>
                        PDO::ERRMODE_EXCEPTION,

                    PDO::ATTR_DEFAULT_FETCH_MODE =>
                        PDO::FETCH_ASSOC,

                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        }

        return self::$connection;
declare(strict_types=1);

namespace app\Support;

use PDO;
use PDOException;
use Throwable;

final class Database
{
    public static function connect(array $config): PDO
    {
        $db = new PDO(
            "mysql:host={$config['host']};port={$config['port']};dbname={$config['name']};charset=utf8mb4",
            $config['user'],
            $config['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        $db->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');

        return $db;
    }

    public static function transaction(PDO $db, callable $work): mixed
    {
        for ($attempt = 1; ; $attempt++) {
            $db->beginTransaction();

            try {
                $result = $work();
                $db->commit();

                return $result;
            } catch (Throwable $error) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }

                // InnoDB có thể chọn rollback một transaction khi hai phiên cùng ghi.
                $deadlock = $error instanceof PDOException
                    && (int) ($error->errorInfo[1] ?? 0) === 1213;

                if (!$deadlock || $attempt === 3) {
                    throw $error;
                }

                usleep(10000 * $attempt);
            }
        }
    }
}