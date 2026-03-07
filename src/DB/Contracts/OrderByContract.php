<?php
namespace App\DB\Contracts;

use App\DB\Tools\Enum\OrderDirection;

/**
 * Контракт для построения SQL‑сортировки ORDER BY.
 */
interface OrderByContract {
    /**
     * Устанавливает сортировку.
     *
     * @param string|array $column Имя колонки или массив вида [['col', OrderDirection::ASC],['col2', OrderDirection::DESC]]
     * @param OrderDirection $direction Направление сортировки, если $column строка
     */
    public function set(string|array $column, OrderDirection $direction = OrderDirection::ASC): void;

    /**
     * Возвращает SQL‑фрагмент ORDER BY.
     *
     * @return string SQL‑код (например "ORDER BY id ASC, name DESC")
     */
    public function getSql(): string;
}
