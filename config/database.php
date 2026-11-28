<?php

declare(strict_types=1);

// Lay cau hinh tu bien moi truong.
$host = getenv('DB_HOST') ?: '127.0.0.1';

$port = (int) (getenv('DB_PORT') ?: 3306);

$name = getenv('DB_NAME') ?: 'ct_tech_store_dev';

$user = getenv('DB_USER') ?: 'root';

$password = getenv('DB_PASSWORD');

// Cau hinh ket noi Database.
return [

    'host' => $host,

    'port' => $port,

    'name' => $name,

    'dbname' => $name,

    'user' => $user,

    'username' => $user,

    'password' => $password !== false
        ? $password
        : 'mysql',

    'charset' => 'utf8mb4',

];