<?php
namespace App\DB\Select;

use App\DB;
use App\DB\Tools\Enum\JoinOperator;
use App\DB\Tools\Enum\JoinType;
use InvalidArgumentException;

/**
 * Класс для построения SQL‑соединений JOIN.
 */
class Join {
    /** @var array Список JOIN‑условий */
    private array $joins = [];

    /**
     * Добавляет JOIN‑условие.
     *
     * @param string|array $table Имя таблицы или массив [table, alias]
     * @param string $first Левая часть условия
     * @param JoinOperator $operator Оператор (=, <>, >, < и т.д.)
     * @param string $second Правая часть условия
     * @param JoinType $type Тип соединения (INNER, LEFT, RIGHT, FULL)
     */
    public function add(
        string|array $table,
        string $first,
        string $second,
        JoinOperator $operator = JoinOperator::EQ,
        JoinType $type = JoinType::INNER
    ): void {
        if (empty($first) || empty($second)) {
            throw new InvalidArgumentException("Join condition parts cannot be empty");
        }

        // поддержка алиаса
        if (is_string($table)) {
            if (empty($table)) {
                throw new InvalidArgumentException("Join table cannot be empty");
            }
            $tbl = $table;
        } elseif (is_array($table)) {
            if (count($table) !== 2 || empty($table[0]) || empty($table[1])) {
                throw new InvalidArgumentException("Join table array must be [table, alias]");
            }
            [$t, $alias] = $table;
            $tbl = "{$t} AS {$alias}";
        } else {
            throw new InvalidArgumentException("Join::add() accepts only string or [table, alias] array");
        }

        $this->joins[] = "{$type->value} JOIN {$tbl} ON {$first} {$operator->value} {$second}";
        DB::getToSql()?->add('join', $this->getSql());
    }

    /**
     * Возвращает SQL‑фрагмент JOIN.
     *
     * @return string SQL‑код
     */
    public function getSql(): string {
        return !empty($this->joins) ? " " . implode(' ', $this->joins) : '';
    }
}
