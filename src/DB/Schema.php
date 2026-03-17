<?php
namespace App\DB;

use App\DB;
use App\DB\Blueprint;
use Throwable;
use App\DB\Contracts\SchemaContract;

class Schema implements SchemaContract {

    /**
     * Создаёт таблицу или синхронизирует её структуру.
     *
     * @param string   $table    Имя таблицы
     * @param callable $callback Коллбэк, принимающий Blueprint для описания колонок
     */
    public static function create(string $table, callable $callback): void {
        $blueprint = new Blueprint();
        $callback($blueprint);

        try {
            $exists = DB::query("SHOW TABLES LIKE ?", [$table])->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($exists)) {
                $columnsSql = [];
                foreach ($blueprint->columns as $col => $def) {
                    $columnsSql[] = "`$col` $def";
                }

                $sql = "CREATE TABLE `$table` (" . implode(", ", $columnsSql) . ")";
                DB::query($sql);
                echo "🆕 Создана таблица $table\n";
            } else {
                // Синхронизация колонок
                $cols = DB::query("SHOW COLUMNS FROM `$table`")->fetchAll(\PDO::FETCH_ASSOC);
                $existingCols = array_column($cols, 'Field');

                foreach ($blueprint->columns as $col => $def) {
                    if (!in_array($col, $existingCols)) {
                        DB::query("ALTER TABLE `$table` ADD `$col` $def");
                        echo "➕ Добавлен столбец $col в $table\n";
                    }
                }

                foreach ($existingCols as $col) {
                    if (!isset($blueprint->columns[$col])) {
                        DB::query("ALTER TABLE `$table` DROP COLUMN `$col`");
                        echo "➖ Удалён столбец $col из $table\n";
                    }
                }
            }
        } catch (Throwable $e) {
            echo "❌ Ошибка при обработке таблицы $table: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Добавляет или синхронизирует индексы и внешние ключи для таблицы.
     *
     * @param string   $table    Имя таблицы
     * @param callable $callback Коллбэк, принимающий Blueprint для описания индексов и ключей
     */
    public static function table(string $table, callable $callback): void {
        $blueprint = new Blueprint();
        $callback($blueprint);

        try {
            // Список существующих индексов
            $indexes = DB::query("SHOW INDEX FROM `$table`")->fetchAll(\PDO::FETCH_ASSOC);
            $existingIndexes = array_column($indexes, 'Key_name');

            // Добавляем недостающие индексы/ключи
            foreach ($blueprint->indexes as $indexDef) {
                preg_match('/(?:INDEX|UNIQUE|FOREIGN KEY)\s+`?(\w+)`?/i', $indexDef, $matches);
                $indexName = $matches[1] ?? null;

                if ($indexName && !in_array($indexName, $existingIndexes)) {
                    DB::query("ALTER TABLE `$table` ADD $indexDef");
                    echo "➕ Добавлен индекс/ключ $indexName в $table\n";
                }
            }

            // Удаляем лишние индексы (кроме PRIMARY)
            foreach ($existingIndexes as $indexName) {
                if ($indexName === 'PRIMARY') continue;

                $found = false;
                foreach ($blueprint->indexes as $indexDef) {
                    if (strpos($indexDef, $indexName) !== false) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    DB::query("ALTER TABLE `$table` DROP INDEX `$indexName`");
                    echo "➖ Удалён индекс $indexName из $table\n";
                }
            }

            // Синхронизация внешних ключей
            $fkList = DB::query("
                SELECT CONSTRAINT_NAME 
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_NAME = ? AND CONSTRAINT_SCHEMA = DATABASE() 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [$table])->fetchAll(\PDO::FETCH_ASSOC);
            $existingFKs = array_column($fkList, 'CONSTRAINT_NAME');
            // Добавляем недостающие FK
            foreach ($blueprint->foreignKeys as $fkDef) {
                preg_match('/CONSTRAINT\s+`?(\w+)`?/i', $fkDef, $matches);
                $fkName = $matches[1] ?? null;

                if ($fkName && !in_array($fkName, $existingFKs)) {
                    DB::query("ALTER TABLE `$table` ADD $fkDef");
                    echo "➕ Добавлен внешний ключ $fkName в $table\n";
                }
            }
            // Удаляем лишние FK
            foreach ($existingFKs as $fkName) {
                $found = false;
                foreach ($blueprint->foreignKeys as $fkDef) {
                    if (strpos($fkDef, $fkName) !== false) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    DB::query("ALTER TABLE `$table` DROP FOREIGN KEY `$fkName`");
                    echo "➖ Удалён внешний ключ $fkName из $table\n";
                }
            }

        } catch (Throwable $e) {
            echo "❌ Ошибка при обработке индексов/ключей в $table: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Удаляет таблицу, если она существует.
     * Перед удалением снимает все внешние ключи.
     * Возвращает true, если таблица удалена, false — если не удалось.
     *
     * @param string $table Имя таблицы
     * @return bool
     */
    public static function dropIfExists(string $table): bool
    {
        try {
            // Проверяем наличие таблицы
            $exists = DB::query("SHOW TABLES LIKE ?", [$table])->fetchAll(\PDO::FETCH_ASSOC);
            if (empty($exists)) {
                echo "ℹ️ Таблица $table не найдена, пропускаем удаление\n";
                return true;
            }

            // Снимаем все внешние ключи внутри таблицы
            $fkList = DB::query("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_NAME = ? 
              AND CONSTRAINT_SCHEMA = DATABASE() 
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$table])->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($fkList as $fk) {
                $fkName = $fk['CONSTRAINT_NAME'];
                DB::query("ALTER TABLE `$table` DROP FOREIGN KEY `$fkName`");
                echo "➖ Удалён внешний ключ $fkName из $table\n";
            }

            // Снимаем внешние ключи в других таблицах, которые ссылаются на эту
            $refList = DB::query("
            SELECT TABLE_NAME, CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE REFERENCED_TABLE_NAME = ? 
              AND CONSTRAINT_SCHEMA = DATABASE()
        ", [$table])->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($refList as $ref) {
                $refTable = $ref['TABLE_NAME'];
                $fkName   = $ref['CONSTRAINT_NAME'];
                DB::query("ALTER TABLE `$refTable` DROP FOREIGN KEY `$fkName`");
                echo "➖ Удалён внешний ключ $fkName из $refTable (ссылается на $table)\n";
            }

            // Удаляем таблицу
            DB::query("DROP TABLE IF EXISTS `$table`");
            echo "🗑️ Таблица $table удалена\n";
            return true;
        } catch (Throwable $e) {
            echo "❌ Ошибка при удалении таблицы $table: " . $e->getMessage() . "\n";
            return false;
        }
    }



    /**
     * Создаёт триггер для таблицы.
     *
     * @param string $name   Имя триггера
     * @param string $timing BEFORE|AFTER
     * @param string $event  INSERT|UPDATE|DELETE
     * @param string $table  Имя таблицы
     * @param string $body   Тело триггера (SQL‑код)
     */
    public static function trigger(string $name, string $timing, string $event, string $table, string $body): void {
        DB::query("DROP TRIGGER IF EXISTS `$name`");
        $sql = "CREATE TRIGGER `$name` $timing $event ON `$table` FOR EACH ROW BEGIN $body END;";
        DB::query($sql);
        echo "⚡ Создан триггер $name ($event ON $table)\n";
    }

    /**
     * Удаляет триггер, если он существует.
     *
     * @param string $name Имя триггера
     */
    public static function dropTrigger(string $name): void
    {
        try {
            $exists = DB::query("
            SELECT TRIGGER_NAME 
            FROM INFORMATION_SCHEMA.TRIGGERS 
            WHERE TRIGGER_SCHEMA = DATABASE() 
              AND TRIGGER_NAME = ?
        ", [$name])->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($exists)) {
                echo "ℹ️ Триггер $name не найден, пропускаем удаление\n";
                return;
            }

            DB::query("DROP TRIGGER IF EXISTS `$name`");
            echo "🗑️ Триггер $name удалён\n";
        } catch (Throwable $e) {
            echo "❌ Ошибка при удалении триггера $name: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Создаёт стандартные триггеры для автоматического обновления
     * временных меток created_at и updated_at.
     *
     * @param string $table Имя таблицы
     */
    public static function timestampTriggers(string $table): void {
        self::trigger("{$table}_set_created_at",'BEFORE','INSERT',$table,
            "SET NEW.created_at = UNIX_TIMESTAMP();
             SET NEW.updated_at = UNIX_TIMESTAMP();"
        );

        self::trigger("{$table}_set_updated_at",'BEFORE','UPDATE',$table,
            "SET NEW.updated_at = UNIX_TIMESTAMP();"
        );
    }

    /**
     * Создаёт представление (VIEW).
     *
     * @param string $name      Имя представления
     * @param string $selectSql SQL‑запрос для наполнения представления
     */
    public static function view(string $name, string $selectSql): void {
        DB::query("DROP VIEW IF EXISTS `$name`");
        $sql = "CREATE VIEW `$name` AS $selectSql";
        DB::query($sql);
        echo "👁️ Создано представление $name\n";
    }

    /**
     * Удаляет представление, если оно существует.
     *
     * @param string $name Имя представления
     */
    public static function dropView(string $name): void
    {
        try {
            $exists = DB::query("
            SELECT TABLE_NAME 
            FROM INFORMATION_SCHEMA.VIEWS 
            WHERE TABLE_SCHEMA = DATABASE() 
              AND TABLE_NAME = ?
        ", [$name])->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($exists)) {
                echo "ℹ️ Представление $name не найдено, пропускаем удаление\n";
                return;
            }

            DB::query("DROP VIEW IF EXISTS `$name`");
            echo "🗑️ Представление $name удалено\n";
        } catch (Throwable $e) {
            echo "❌ Ошибка при удалении представления $name: " . $e->getMessage() . "\n";
        }
    }
}
