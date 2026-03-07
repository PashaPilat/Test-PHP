<?php
namespace App\DB\Contracts;

/**
 * Контракт для построения SQL‑смещения OFFSET.
 */
interface OffsetContract {
    /**
     * Минимальное значение для OFFSET.
     */
    public const MIN_OFFSET = 0;

    /**
     * Устанавливает значение OFFSET.
     *
     * @param int $count Смещение (должно быть >= MIN_OFFSET)
     * @throws \InvalidArgumentException Если $count < MIN_OFFSET
     */
    public function set(int $count): void;

    /**
     * Возвращает SQL‑фрагмент OFFSET.
     *
     * @return string SQL‑код (например "OFFSET 20")
     */
    public function getSql(): string;
}
