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
    }
}