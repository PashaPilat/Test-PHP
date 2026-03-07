<?php
namespace App\DB\Contracts;

use App\DB\Tools\Enum\JoinOperator;
use App\DB\Tools\Enum\JoinType;

/**
 * Контракт для построения SQL‑соединений JOIN.
 */
interface JoinContract {
    /**
     * Добавляет JOIN‑условие.
     *
     * @param string|array $table Имя таблицы или массив [table, alias]
     * @param string $first Левая часть условия
     * @param string $second Правая часть условия
     * @param JoinOperator $operator Оператор (=, <>, >, < и т.д.)
     * @param JoinType $type Тип соединения (INNER, LEFT, RIGHT, FULL)
     */
    public function add(
        string|array $table,
        string $first,
        string $second,
        JoinOperator $operator,
        JoinType $type = JoinType::INNER
    ): void;

    /**
     * Возвращает SQL‑фрагмент JOIN.
     *
     * @return string SQL‑код
     */
    public function getSql(): string;
}
