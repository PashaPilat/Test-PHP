<?php
namespace App\DB\Contracts;

/**
 * Контракт для построения SQL‑группировки GROUP BY.
 */
interface GroupByContract {
    /**
     * Добавляет колонку или список колонок для группировки.
     *
     * @param string|array $column Имя колонки или массив имён колонок
     * @throws \InvalidArgumentException Если значение пустое или содержит пустые строки
     */
    public function add(string|array $column): void;

    /**
     * Возвращает SQL‑фрагмент GROUP BY.
     *
     * @return string SQL‑код (например "GROUP BY status, type")
     */
    public function getSql(): string;
}
