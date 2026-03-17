<?php
define('BASE_PATH', __DIR__); // корень проекта
require __DIR__ . '/src/Core/Helpers.php';
require __DIR__ . '/vendor/autoload.php';

use App\DB;
use App\Migration;

$config = require __DIR__ . '/config/db.php';
DB::connect($config);

// Собираем список файлов миграций
$migrationsPath = __DIR__ . "/migrations/*.php";
$migrationFiles = glob($migrationsPath);

if (empty($migrationFiles)) {
    echo "❌ Нет миграций для выполнения.\n";
    exit(0);
}

// Определяем режим (up или down)
$mode = $argv[1] ?? 'up';
echo "🔧 Запуск миграций в режиме: $mode...\n";

// ------------------------------
// Логика отложенного удаления
// ------------------------------
// При down мы можем столкнуться с зависимостями FK.
// Поэтому делаем несколько проходов, откладывая проблемные таблицы.
$maxRetries = 4;      // максимум попыток
$retryCount = 0;      // текущая попытка
$pendingDrops = [];   // список миграций, которые не удалось откатить

do {
    $retryCount++;
    $pendingDrops = [];

    foreach ($migrationFiles as $file) {
        require_once $file;

        // Преобразуем имя файла в имя класса миграции
        $base = basename($file, ".php");
        $base = preg_replace('/^\d+_/', '', $base);
        $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $base)));

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
                // down может вернуть true/false (успех/неудача)
                $ok = $migration->down();
                if ($ok === false) {
                    $pendingDrops[] = $className;
                    echo "⏳ Миграция $className отложена (FK зависимость).\n";
                } else {
                    echo "↩️ Миграция $className откатена.\n";
                }
            }
        } catch (Throwable $e) {
            echo "❌ Ошибка в миграции $className: " . $e->getMessage() . "\n";
            // если ошибка — откладываем для повторной попытки
            $pendingDrops[] = $className;
        }
    }

    if (!empty($pendingDrops)) {
        echo "⏳ Остались миграции для повторного удаления: " . implode(', ', $pendingDrops) . "\n";
    }
} while (!empty($pendingDrops) && $retryCount < $maxRetries);

// Если после всех попыток остались проблемные таблицы
if (!empty($pendingDrops)) {
    echo "⚠️ Эти миграции не удалось откатить автоматически: " . implode(', ', $pendingDrops) . "\n";
    echo "Удалите их вручную.\n";
}

echo "🏁 Все миграции обработаны.\n";
