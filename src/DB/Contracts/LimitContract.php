<?php
namespace App\DB\Contracts;

/**
 * Контракт для построения SQL‑ограничения LIMIT.
 */
interface LimitContract {
    /**
     * Минимальное значение для LIMIT.
     */
    public const MIN_LIMIT = 1;

    /**
     * Устанавливает значение LIMIT.
     *
     * @param int $count Количество строк (должно быть >= MIN_LIMIT)
     * @throws \InvalidArgumentException Если $count < MIN_LIMIT
     */
    public function set(int $count): void;

    /**
     * Возвращает SQL‑фрагмент LIMIT.
     *
     * @return string SQL‑код (например "LIMIT 10")
     */
    public function getSql(): string;
}
