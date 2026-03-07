<?php
$host = getenv('DB_HOST') ?: (
    php_sapi_name() === 'cli' && stripos(PHP_OS, 'WIN') !== false
        ? '127.0.0.1'
        : 'mysql'
);

return [
    'dsn'  => "mysql:host={$host};port=3306;dbname=testphp;charset=utf8",
    'user' => getenv('DB_USER') ?: 'testuser',
    'pass' => getenv('DB_PASS') ?: 'pass',
];
