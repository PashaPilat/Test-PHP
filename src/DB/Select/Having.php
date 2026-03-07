<?php
namespace App\DB\Select;

use App\DB;
use App\DB\Contracts\HavingContract;
use App\DB\Tools\Enum\Boolean;

use InvalidArgumentException;

/**
 * Класс для построения SQL‑условий HAVING.
 * Поддерживает группы условий, алиасы для удобства и безопасную работу с параметрами.
 */
class Having implements HavingContract {
    /** @var array Список условий HAVING */
    private array $conditions = [];

    /** @var array Параметры для условий */
    private array $params = [];

    /** @var array Стек групп условий */
    private array $groupStack = [];

    /**
     * Добавляет условие HAVING.
     *
     * @param string $condition SQL‑условие (например "COUNT(id) > ?")
     * @param array $params Параметры для условия
     * @param Boolean $boolean Логическая связка (AND/OR)
     * @throws InvalidArgumentException Если условие пустое
     */
    public function add(string $condition, array $params = [], Boolean $boolean = Boolean::AND): void {
        if (empty($condition)) {
            throw new InvalidArgumentException("Having condition cannot be empty");
        }

        $prefix = empty($this->conditions) ? '' : " {$boolean->value} ";
        if (!empty($this->groupStack)) {
            $group =& $this->groupStack[count($this->groupStack)-1];
            if (!empty($group['conditions'])) {
                $condition = "{$boolean->value} {$condition}";
            }
            $group['conditions'][] = $condition;
        } else {
            $this->conditions[] = $prefix . $condition;
        }

        $this->params = array_merge($this->params, $params);
        DB::getToSql()?->add('having', $this->getSql(), $this->params);
    }

    /**
     * Начинает группу условий HAVING.
     *
     * @param Boolean $boolean Логическая связка для группы (AND/OR)
     */
    public function group(Boolean $boolean = Boolean::AND): void {
        $this->groupStack[] = ['boolean' => $boolean->value, 'conditions' => []];
    }

    /**
     * Завершает группу условий HAVING и добавляет её в общий список.
     */
    public function groupEnd(): void {
        $group = array_pop($this->groupStack);
        if ($group) {
            $sql = "(" . implode(' ', $group['conditions']) . ")";
            if (!empty($this->conditions)) {
                $sql = $group['boolean'] . " " . $sql;
            }
            $this->conditions[] = $sql;
        }
        DB::getToSql()?->add('having', $this->getSql(), $this->params);
    }

    /**
     * Алиас для группы с AND.
     */
    public function andGroup(): void {
        $this->group(Boolean::AND);
    }

    /**
     * Алиас для группы с OR.
     */
    public function orGroup(): void {
        $this->group(Boolean::OR);
    }

    /**
     * Возвращает SQL‑фрагмент HAVING.
     *
     * @return string SQL‑код
     */
    public function getSql(): string {
        return !empty($this->conditions) ? " HAVING " . implode('', $this->conditions) : '';
    }

    /**
     * Возвращает параметры для HAVING.
     *
     * @return array Массив параметров
     */
    public function getParams(): array {
        return $this->params;
    }
}
