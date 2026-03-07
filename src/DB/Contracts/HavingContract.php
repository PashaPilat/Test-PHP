<?php
namespace App\DB\Contracts;

use App\DB\Tools\Enum\Boolean;

/**
 * Контракт для построения SQL‑условий HAVING.
 */
interface HavingContract {
    /**
     * Добавляет условие HAVING.
     *
     * @param string $condition SQL‑условие (например "COUNT(id) > ?")
     * @param array $params Параметры для условия
     * @param Boolean $boolean Логическая связка (AND/OR)
     */
    public function add(string $condition, array $params = [], Boolean $boolean = Boolean::AND): void;

    /**
     * Начинает группу условий HAVING.
     *
     * @param Boolean $boolean Логическая связка для группы (AND/OR)
     */
    public function group(Boolean $boolean = Boolean::AND): void;

    /**
     * Завершает группу условий HAVING.
     */
    public function groupEnd(): void;

    /**
     * Алиас для группы с AND.
     */
    public function andGroup(): void;

    /**
     * Алиас для группы с OR.
     */
    public function orGroup(): void;

    /**
     * Возвращает SQL‑фрагмент HAVING.
     */
    public function getSql(): string;

    /**
     * Возвращает параметры для HAVING.
     */
    public function getParams(): array;
}
