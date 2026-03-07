<?php
namespace App\DB\Select;

use App\DB;
use App\DB\Contracts\GroupByContract;
use InvalidArgumentException;

/**
 * Класс для построения SQL‑группировки GROUP BY.
 */
class GroupBy implements GroupByContract {
    /** @var array Список колонок для группировки */
    private array $columns = [];

    /**
     * Добавляет колонку или список колонок для группировки.
     *
     * @param string|array $column Имя колонки или массив имён колонок
     * @throws InvalidArgumentException Если значение пустое или содержит пустые строки
     */
    public function add(string|array $column): void {
        if (is_string($column)) {
            if (empty($column)) {
                throw new InvalidArgumentException("GroupBy column cannot be empty");
            }
            $this->columns[] = $column;
        } elseif (is_array($column)) {
            if (empty($column)) {
                throw new InvalidArgumentException("GroupBy columns list cannot be empty");
            }
            foreach ($column as $col) {
                if (empty($col)) {
                    throw new InvalidArgumentException("GroupBy column in array cannot be empty");
                }
                $this->columns[] = $col;
            }
        } else {
            throw new InvalidArgumentException("GroupBy add() accepts only string or array");
        }

        DB::getToSql()?->add('group', $this->getSql());
    }

    /**
     * Возвращает SQL‑фрагмент GROUP BY.
     *
     * @return string SQL‑код (например "GROUP BY status, type")
     */
    public function getSql(): string {
        return !empty($this->columns) ? " GROUP BY " . implode(',', $this->columns) : '';
    }
}
