<?php
declare(strict_types=1);
$config = require dirname(__DIR__) . '/config/database.php';
foreach (['host', 'port', 'name', 'user', 'password'] as $key) {
    if (!array_key_exists($key, $config)) {
        fwrite(STDERR, "FAIL: missing config key $key\n"); exit(1);
    }
}
if (!is_int($config['port']) || $config['port'] < 1 || $config['port'] > 65535) {
    fwrite(STDERR, "FAIL: invalid DB_PORT\n"); exit(1);
}
echo "PASS: database config structure (no connection attempted)\n";
