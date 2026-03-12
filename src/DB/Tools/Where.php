<?php

namespace App\DB\Tools;

use App\DB;
use App\DB\Contracts\WhereContract;
use App\DB\Tools\Enum\WhereOperator;
use App\DB\Tools\Enum\Boolean;
use InvalidArgumentException;

/**
 * Класс для построения условий WHERE в SQL‑запросах.
 * Поддерживает группы условий, алиасы для частых проверок и безопасную работу с параметрами.
 */
class Where implements WhereContract
{
    /** 
     * @var array Условия WHERE (строки SQL‑фрагментов)
     */
    private array $conditions = [];

    /** 
     * @var array Параметры для условий (значения для плейсхолдеров ?)
     */
    private array $params = [];

    /** 
     * @var array Стек групп условий (для вложенных скобок)
     */
    private array $groupStack = [];

    /**
     * Добавляет условие в WHERE.
     *
     * @param string $col Имя колонки
     * @param mixed $val Значение
     * @param WhereOperator $operator Оператор сравнения (например =, <>, LIKE, IN)
     * @param Boolean $boolean Логический оператор (AND/OR)
     * @throws InvalidArgumentException Если имя колонки пустое
     */
    public function add(
        string $col,
        mixed $val,
        WhereOperator $operator = WhereOperator::EQ,
        Boolean $boolean = Boolean::AND
    ): void {
        if (empty($col)) {
            throw new InvalidArgumentException("Column name cannot be empty");
        }

        if ($operator === WhereOperator::IS_NULL || $operator === WhereOperator::IS_NOT_NULL) {
            $condition = "{$col} {$operator->value}";
        } else {
            $condition = "{$col} {$operator->value} ?";
            $this->params[] = $val;
        }

        if (!empty($this->groupStack)) {
            $group = &$this->groupStack[count($this->groupStack) - 1];
            if (!empty($group['conditions'])) {
                $condition = $boolean->value . " " . $condition;
            }
            $group['conditions'][] = $condition;
        } else {
            if (!empty($this->conditions)) {
                $condition = $boolean->value . " " . $condition;
            }
            $this->conditions[] = $condition;
        }

        DB::getToSql()?->add('where', $this->getSql());
    }

    /**
     * Начинает группу условий.
     *
     * @param Boolean $boolean Логический оператор для группы (AND/OR)
     */
    public function group(Boolean $boolean = Boolean::AND): void
    {
        $this->groupStack[] = ['boolean' => $boolean->value, 'conditions' => []];
    }

    /**
     * Завершает группу условий и добавляет её в общий список.
     */
    public function groupEnd(): void
    {
        $group = array_pop($this->groupStack);
        if ($group) {
            $sql = "(" . implode(' ', $group['conditions']) . ")";
            if (!empty($this->conditions)) {
                $sql = $group['boolean'] . " " . $sql;
            }
            $this->conditions[] = $sql;
        }
        DB::getToSql()?->add('where', $this->getSql());
    }

    /**
     * Удобный алиас для группы с AND.
     */
    public function andGroup(): void
    {
        $this->group(Boolean::AND);
    }

    /**
     * Удобный алиас для группы с OR.
     */
    public function orGroup(): void
    {
        $this->group(Boolean::OR);
    }

    /**
     * Добавляет условие "status = 'active'" или "status <> 'active'".
     *
     * @param bool $flag true для активных, false для неактивных
     */
    public function whereIsActive(bool $flag): void
    {
        if ($flag) {
            $this->add('status', 'active', WhereOperator::EQ);
        } else {
            $this->add('status', 'active', WhereOperator::NEQ);
        }
    }

    /**
     * Добавляет условие "id = ?".
     *
     * @param int $id Идентификатор
     */
    public function whereById(int $id): void
    {
        $this->add('id', $id, WhereOperator::EQ);
    }

    /**
     * Возвращает SQL‑строку WHERE.
     *
     * @return string SQL‑фрагмент
     */
    public function getSql(): string
    {
        return !empty($this->conditions) ? "WHERE " . implode(' ', $this->conditions) : '';
    }

    /**
     * Возвращает параметры для подготовленного запроса.
     *
     * @return array Массив параметров
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
