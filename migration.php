<?php
define('BASE_PATH', __DIR__); // корень проекта
require __DIR__ . '/src/Core/Helpers.php';
require __DIR__ . '/vendor/autoload.php';

use App\DB;
use App\Migration;

$config = require __DIR__ . '/config/db.php';
DB::connect($config);

$migrationsPath = __DIR__ . "/migrations/*.php";
$migrationFiles = glob($migrationsPath);

if (empty($migrationFiles)) {
    echo "❌ Нет миграций для выполнения.\n";
    exit(0);
}

$mode = $argv[1] ?? 'up'; // по умолчанию выполняем up

echo "🔧 Запуск миграций в режиме: $mode...\n";

foreach ($migrationFiles as $file) {
    require_once $file;

    $base = basename($file, ".php");
    $base = preg_replace('/^\d+_/', '', $base); // Убираем префикс с цифрами
    $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $base))); // Преобразуем в CamelCase

    if (!class_exists($className)) {
        echo "⚠️ Класс $className не найден в файле $file\n";
        continue;
    }

    try {
        /** @var Migration $migration */
        $migration = new $className();
        if ($mode === 'up') {
            $migration->up();
            echo "✅ Миграция $className применена.\n";
        } elseif ($mode === 'down') {
            $migration->down();
            echo "↩️ Миграция $className откатена.\n";
        }
    } catch (Throwable $e) {
        echo "❌ Ошибка в миграции $className: " . $e->getMessage() . "\n";
    }
}

echo "🏁 Все миграции обработаны.\n";
