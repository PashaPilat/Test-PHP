<?php
namespace App\DB\Contracts;

/**
 * Контракт для построения SQL‑части FROM.
 */
interface FromContract {
    /**
     * Устанавливает таблицу для выборки.
     *
     * @param string|array $table Имя таблицы или массив [table, alias]
     * @throws \InvalidArgumentException Если имя таблицы пустое или массив некорректен
     */
    public function set(string|array $table): void;

    /**
     * Возвращает имя таблицы (с алиасом, если задан).
     *
     * @return string|null Имя таблицы или null, если не установлено
     */
    public function get(): ?string;
}
