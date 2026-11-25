<?php

require_once __DIR__
    . '/../app/Support/Database.php';

use App\Support\Database;

try {
    $pdo = Database::connect();

    echo "PASS: Ket noi PDO thanh cong!" . PHP_EOL;

    echo "Database: "
        . $pdo->query("SELECT DATABASE()")->fetchColumn()
        . PHP_EOL;

    echo "MySQL version: "
        . $pdo->query("SELECT VERSION()")->fetchColumn()
        . PHP_EOL;

    echo "SELECT 1 = "
        . $pdo->query("SELECT 1")->fetchColumn()
        . PHP_EOL;

    $tables = $pdo->query(
        "SELECT COUNT(*)
         FROM information_schema.tables
         WHERE table_schema = DATABASE()
         AND table_type = 'BASE TABLE'"
    )->fetchColumn();

    echo "Tong so bang: " . $tables . PHP_EOL;

} catch (Throwable $e) {

    echo "FAIL: Ket noi PDO that bai!" . PHP_EOL;
    echo "Loi: " . $e->getMessage() . PHP_EOL;

    exit(1);
}