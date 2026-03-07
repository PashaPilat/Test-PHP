<?php
namespace App\DB\Select;

use App\DB;
use App\DB\Contracts\FromContract;
use InvalidArgumentException;

/**
 * Класс для построения SQL‑части FROM.
 */
class From implements FromContract {
    /** @var string|null Имя таблицы (с алиасом, если задан) */
    private ?string $table = null;

    /**
     * Устанавливает таблицу для выборки.
     *
     * @param string|array $table Имя таблицы или массив [table, alias]
     * @throws InvalidArgumentException Если имя таблицы пустое или массив некорректен
     */
    public function set(string|array $table): void {
        if (is_string($table)) {
            if (empty($table)) {
                throw new InvalidArgumentException("Table name cannot be empty");
            }
            $this->table = $table;
            DB::getToSql()?->add('from', "FROM {$table}");
        } elseif (is_array($table)) {
            if (count($table) !== 2 || empty($table[0]) || empty($table[1])) {
                throw new InvalidArgumentException("Array must be [table, alias] with non-empty values");
            }
            [$tbl, $alias] = $table;
            $this->table = "{$tbl} AS {$alias}";
            DB::getToSql()?->add('from', "FROM {$tbl} AS {$alias}");
        } else {
            throw new InvalidArgumentException("From::set() accepts only string or [table, alias] array");
        }
    }

    /**
     * Возвращает имя таблицы (с алиасом, если задан).
     *
     * @return string|null Имя таблицы или null, если не установлено
     */
    public function get(): ?string {
        return $this->table;
    }
}
