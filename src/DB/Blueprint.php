<?php

namespace App\DB;

use App\DB\Contracts\BlueprintContract;

/**
 * Класс Blueprint описывает структуру таблицы для миграций.
 * Позволяет добавлять столбцы разных типов и индексы.
 */
class Blueprint implements BlueprintContract
{
    /** @var array Список определённых колонок (name => SQL definition) */
    public array $columns = [];
    /** @var array Список индексов */
    public array $indexes = [];
    /** @var array Список внешних ключей */
    public array $foreignKeys = [];

    /**
     * Добавляет колонку в список.
     *
     * @param string $name Имя столбца
     * @param string $definition SQL‑определение столбца
     */
    private function addColumn(string $name, string $definition): void
    {
        $this->columns[$name] = $definition;
    }

    /**
     * Создаёт поле id с гибкими параметрами.
     *
     * @param int|null $length длина INT (например 10), можно null
     * @param bool $unsigned использовать UNSIGNED (по умолчанию true)
     * @param bool $autoIncrement использовать AUTO_INCREMENT (по умолчанию true)
     * @param bool $primary использовать PRIMARY KEY (по умолчанию true)
     * @param string $comment комментарий для поля
     */
    public function id(
        int $length = null,
        bool $unsigned = true,
        bool $autoIncrement = true,
        bool $primary = true,
        string $comment = 'Идентификатор'
    ): void {
        $len = $length ? "($length)" : "";
        $uns = $unsigned ? "UNSIGNED " : "";
        $auto = $autoIncrement ? "AUTO_INCREMENT " : "";
        $pk = $primary ? "PRIMARY KEY " : "";
        $this->addColumn(
            'id',
            "INT{$len} {$uns}{$auto}{$pk}COMMENT '{$comment}'"
        );
    }

    /**
     * Создаёт поле типа INT.
     *
     * @param string $name имя поля
     * @param int|null $length длина INT (например 10), можно null
     * @param bool $unsigned использовать UNSIGNED (по умолчанию true)
     * @param bool $nullable допускается NULL (по умолчанию false)
     * @param mixed $default значение по умолчанию (по умолчанию null)
     * @param string $comment комментарий для поля
     */
    public function integer(
        string $name,
        int $length = null,
        bool $unsigned = true,
        bool $nullable = false,
        $default = null,
        string $comment = ''
    ): void {
        $len = $length ? "($length)" : "";
        $uns = $unsigned ? "UNSIGNED " : "";
        $null = $nullable ? "NULL " : "NOT NULL ";
        $def = $default !== null ? "DEFAULT " . (is_numeric($default) ? $default : "'$default'") . " " : "";
        $comm = $comment ? "COMMENT '$comment'" : "";
        $this->addColumn($name, "INT{$len} {$uns}{$null}{$def}{$comm}");
    }


    /**
     * Создаёт поле типа VARCHAR.
     *
     * @param string $name имя поля
     * @param int $length длина VARCHAR (по умолчанию 255)
     * @param bool $nullable допускается NULL (по умолчанию false)
     * @param string|null $default значение по умолчанию
     * @param string $comment комментарий для поля
     */
    public function string(
        string $name,
        int $length = self::DEFAULT_STRING_LENGTH,
        bool $nullable = false,
        ?string $default = null,
        string $comment = '',
        bool $unique = false
    ): void {
        $null = $nullable ? "NULL " : "NOT NULL ";
        $def = $default !== null ? "DEFAULT '$default' " : "";
        $comm = $comment ? "COMMENT '$comment'" : "";
        $this->addColumn($name, "VARCHAR($length) {$null}{$def}{$comm}");
        if ($unique) {
            $this->indexes[] = "UNIQUE `uniq_{$name}` (`$name`)";
        }
    }

    /**
     * Добавляет текстовый столбец (TEXT).
     *
     * @param string $name Имя столбца
     * @param bool $nullable Допускает NULL (по умолчанию true)
     * @param string|null $default Значение по умолчанию (по умолчанию null)
     * @param string $comment Комментарий для поля
     */
    public function text(
        string $name,
        bool $nullable = true,
        ?string $default = null,
        string $comment = ''
    ): void {
        $null = $nullable ? "NULL " : "NOT NULL ";
        $def = $default !== null ? "DEFAULT '$default' " : "";
        $comm = $comment ? "COMMENT '$comment'" : "";
        $this->addColumn($name, "TEXT {$null}{$def}{$comm}");
    }

    /**
     * Добавляет длинный текстовый столбец (LONGTEXT).
     *
     * @param string $name Имя столбца
     * @param bool $nullable Допускает NULL (по умолчанию true)
     * @param string|null $default Значение по умолчанию (по умолчанию null)
     * @param string $comment Комментарий для поля
     */
    public function longText(
        string $name,
        bool $nullable = true,
        ?string $default = null,
        string $comment = ''
    ): void {
        $null = $nullable ? "NULL " : "NOT NULL ";
        $def = $default !== null ? "DEFAULT '$default' " : "";
        $comm = $comment ? "COMMENT '$comment'" : "";
        $this->addColumn($name, "LONGTEXT {$null}{$def}{$comm}");
    }

    /**
     * Добавляет JSON‑столбец.
     *
     * @param string $name Имя столбца
     * @param bool $nullable Допускает NULL (по умолчанию true)
     * @param string $comment Комментарий для поля
     */
    public function json(
        string $name,
        bool $nullable = true,
        string $comment = ''
    ): void {
        $null = $nullable ? "NULL " : "NOT NULL ";
        $comm = $comment ? "COMMENT '$comment'" : "";
        $this->addColumn($name, "JSON {$null}{$comm}");
    }

    /**
     * Добавляет десятичное число (DECIMAL).
     *
     * @param string $name Имя столбца
     * @param int $precision Общее количество цифр (по умолчанию 10)
     * @param int $scale Количество знаков после запятой (по умолчанию 2)
     * @param bool $unsigned UNSIGNED (по умолчанию false)
     * @param string|null $default Значение по умолчанию
     * @param string $comment Комментарий для поля
     */
    public function decimal(
        string $name,
        int $precision = self::DEFAULT_DECIMAL_PRECISION,
        int $scale = self::DEFAULT_DECIMAL_SCALE,
        bool $unsigned = false,
        ?string $default = null,
        string $comment = ''
    ): void {
        $uns = $unsigned ? "UNSIGNED " : "";
        $def = $default !== null ? "DEFAULT $default " : "";
        $comm = $comment ? "COMMENT '$comment'" : "";
        $this->addColumn($name, "DECIMAL($precision,$scale) {$uns}{$def}{$comm}");
    }

    /**
     * Добавляет булевый столбец (TINYINT(1)).
     *
     * @param string $name Имя столбца
     * @param string $default Значение по умолчанию ('0' или '1')
     * @param string $comment Комментарий для поля
     */
    public function boolean(
        string $name,
        string $default = '0',
        string $comment = ''
    ): void {
        $comm = $comment ? "COMMENT '$comment'" : "";
        $this->addColumn($name, "TINYINT(1) NOT NULL DEFAULT $default {$comm}");
    }

    /**
     * Добавляет перечисление (ENUM).
     *
     * @param string $name Имя столбца
     * @param array $values Допустимые значения
     * @param string|null $default Значение по умолчанию
     * @param string $comment Комментарий для поля
     */
    public function enum(
        string $name,
        array $values,
        ?string $default = null,
        string $comment = ''
    ): void {
        $def = $default !== null ? "DEFAULT '$default' " : "";
        $comm = $comment ? "COMMENT '$comment'" : "";
        $this->addColumn($name, "ENUM('" . implode("','", $values) . "') {$def}{$comm}");
    }

    /**
     * Добавляет дату/время (DATETIME).
     *
     * @param string $name Имя столбца
     * @param bool $nullable Допускает NULL (по умолчанию true)
     * @param string|null $default Значение по умолчанию (например CURRENT_TIMESTAMP)
     * @param string $comment Комментарий для поля
     */
    public function datetime(
        string $name,
        bool $nullable = true,
        ?string $default = null,
        string $comment = ''
    ): void {
        $null = $nullable ? "NULL " : "NOT NULL ";
        $def = $default !== null ? "DEFAULT $default " : "";
        $comm = $comment ? "COMMENT '$comment'" : "";
        $this->addColumn($name, "DATETIME {$null}{$def}{$comm}");
    }

    /**
     * Добавляет стандартные временные метки created_at и updated_at
     * в формате UNIX‑timestamp (целое число).
     *
     * created_at — автоматически проставляется при создании записи.
     * updated_at — автоматически обновляется при каждом изменении записи.
     */
    public function timestamps(): void
    {
        // created_at: фиксируется момент вставки
        $this->integer(self::CREATED_AT, false, 0, 'UNIX‑timestamp создания', true);

        // updated_at: фиксируется момент обновления
        $this->integer(self::UPDATED_AT, false, 0, 'UNIX‑timestamp обновления', true);
    }

    /**
     * Создаёт индекс.
     *
     * @param string|array $columns Колонка или массив колонок
     * @param string|null $name Имя индекса
     */
    public function index(string|array $columns, string $name = null): void
    {
        $cols = is_array($columns) ? "`" . implode("`,`", $columns) . "`" : "`$columns`";
        $this->indexes[] = "INDEX " . ($name ? "`$name` " : "") . "($cols)";
    }

    /**
     * Создаёт уникальный индекс.
     */
    public function unique(string|array $columns, string $name = null): void
    {
        $cols = is_array($columns) ? "`" . implode("`,`", $columns) . "`" : "`$columns`";
        $this->indexes[] = "UNIQUE " . ($name ? "`$name` " : "") . "($cols)";
    }

    /**
     * Создаёт внешний ключ.
     *
     * @param string $column Локальная колонка
     * @param string $refTable Таблица‑источник
     * @param string $refColumn Колонка‑источник (по умолчанию id)
     * @param string $onDelete Действие при удалении (CASCADE/SET NULL/RESTRICT)
     */
    public function foreign(string $column, string $refTable, string $refColumn = 'id', string $onDelete = self::CASCADE, string $onUpdate = self::CASCADE, string $name = null): void
    {
        $valid = [self::CASCADE, self::SET_NULL, self::RESTRICT, self::NO_ACTION];
        if (!in_array($onDelete, $valid, true)) {
            throw new \InvalidArgumentException("Недопустимое значение ON DELETE: $onDelete");
        }
        if (!in_array($onUpdate, $valid, true)) {
            throw new \InvalidArgumentException("Недопустимое значение ON UPDATE: $onUpdate");
        }

        $fkName = $name ?? "fk_{$column}_{$refTable}";
        $this->foreignKeys[] =
            "CONSTRAINT `$fkName` FOREIGN KEY (`$column`) REFERENCES `$refTable`(`$refColumn`) ON DELETE $onDelete ON UPDATE $onUpdate";
    }
}

