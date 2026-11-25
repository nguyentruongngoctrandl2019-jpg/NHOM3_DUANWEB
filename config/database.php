<?php

return [
    'host'     => getenv('DB_HOST') ?: '127.0.0.1',
    'port'     => (int) (getenv('DB_PORT') ?: 3306),

    'name'     => getenv('DB_NAME') ?: 'ct_tech_store_dev',
    'dbname'   => getenv('DB_NAME') ?: 'ct_tech_store_dev',

    'user'     => getenv('DB_USER') ?: 'cttech_app',
    'username' => getenv('DB_USER') ?: 'cttech_app',

    'password' => getenv('DB_PASSWORD') ?: '',
    'charset'  => 'utf8mb4'
];