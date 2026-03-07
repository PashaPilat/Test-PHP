<?php
namespace App\DB\Select;

use App\DB;
use App\DB\Contracts\OrderByContract;
use App\DB\Tools\Enum\OrderDirection;
use InvalidArgumentException;

/**
 * Класс для построения SQL‑сортировки ORDER BY.
 */
class OrderBy implements OrderByContract {
    /** @var string|null SQL‑фрагмент ORDER BY */
    private ?string $order = null;

    /**
     * Устанавливает сортировку.
     *
     * @param string|array $column Имя колонки или массив вида [['col',OrderDirection::ASC],['col2',OrderDirection::DESC]]
     * @param OrderDirection $direction Направление сортировки, если $column строка
     * @throws InvalidArgumentException Если массив некорректен
     */
    public function set(string|array $column, OrderDirection $direction = OrderDirection::ASC): void {
        $parts = [];

        if (is_string($column)) {
            if (empty($column)) {
                throw new InvalidArgumentException("OrderBy column cannot be empty");
            }
            $parts[] = "{$column} {$direction->value}";
        } elseif (is_array($column)) {
            if (empty($column)) {
                throw new InvalidArgumentException("OrderBy columns list cannot be empty");
            }
            foreach ($column as $item) {
                if (!is_array($item) || count($item) !== 2) {
                    throw new InvalidArgumentException("OrderBy array must contain [column,OrderDirection] pairs");
                }
                [$col, $dir] = $item;
                if (!$dir instanceof OrderDirection) {
                    throw new InvalidArgumentException("Order direction must be instance of OrderDirection enum");
                }
                if (empty($col)) {
                    throw new InvalidArgumentException("OrderBy column cannot be empty");
                }
                $parts[] = "{$col} {$dir->value}";
            }
        } else {
            throw new InvalidArgumentException("OrderBy set() accepts only string or array");
        }

        $this->order = "ORDER BY " . implode(', ', $parts);
        DB::getToSql()?->add('order', $this->getSql());
    }

    /**
     * Возвращает SQL‑фрагмент ORDER BY.
     *
     * @return string SQL‑код (например "ORDER BY id ASC, name DESC")
     */
    public function getSql(): string {
        return $this->order ? " " . $this->order : '';
    }
}
