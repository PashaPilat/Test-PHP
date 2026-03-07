<?php
namespace App\DB\Select;

use App\DB;
use App\DB\Query;
use App\DB\Tools\Where;
use App\DB\Tools\Enum\WhereOperator;
use App\DB\Tools\Enum\Boolean;
use App\DB\Tools\Enum\JoinOperator;
use App\DB\Tools\Enum\JoinType;
use App\DB\Tools\Enum\OrderDirection;

use InvalidArgumentException;
use App\DB\Contracts\SelectBuilderContract;

/**
 * Класс для построения и выполнения SELECT‑запросов.
 * Поддерживает fluent‑интерфейс, условия WHERE, JOIN, GROUP BY, HAVING, ORDER BY, LIMIT, OFFSET.
 */
class Builder implements SelectBuilderContract {
    /** @var array Список колонок */
    private array $columns = ['*'];

    /** @var From Объект для FROM */
    private From $from;

    /** @var Where Объект для WHERE */
    private Where $where;

    /** @var Join Объект для JOIN */
    private Join $join;

    /** @var GroupBy Объект для GROUP BY */
    private GroupBy $groupBy;

    /** @var Having Объект для HAVING */
    private Having $having;

    /** @var OrderBy Объект для ORDER BY */
    private OrderBy $orderBy;

    /** @var Limit Объект для LIMIT */
    private Limit $limit;

    /** @var Offset Объект для OFFSET */
    private Offset $offset;

    /**
     * Создаёт билдер для SELECT‑запроса.
     *
     * @param array|string $columns Список колонок или одна колонка
     */
    public function __construct(array|string $columns = ['*']) {
        $this->columns = is_string($columns) ? [$columns] : $columns;
        $this->from = new From();
        $this->where = new Where();
        $this->join = new Join();
        $this->groupBy = new GroupBy();
        $this->having = new Having();
        $this->orderBy = new OrderBy();
        $this->limit = new Limit();
        $this->offset = new Offset();
        DB::getToSql()?->add('select', "SELECT " . implode(',', $this->columns));
    }

    /**
     * Указывает таблицу или несколько таблиц для выборки.
     *
     * @param string|array $table Имя таблицы или массив имён
     * @return self
     * @throws InvalidArgumentException Если имя таблицы пустое
     */
    public function from(string|array $table): self {
        if (is_string($table)) {
            if (empty($table)) {
                throw new InvalidArgumentException("Table name cannot be empty");
            }
            $this->from->set($table);
        } elseif (is_array($table)) {
            if (empty($table)) {
                throw new InvalidArgumentException("Table list cannot be empty");
            }
            foreach ($table as $t) {
                if (empty($t)) {
                    throw new InvalidArgumentException("Table name in list cannot be empty");
                }
                $this->from->set($t);
            }
        } else {
            throw new InvalidArgumentException("from() accepts only string or array");
        }
        return $this;
    }
    /** @inheritdoc */
    public function where(string $col, mixed $val, WhereOperator $operator = WhereOperator::EQ): self {
        $this->where->add($col, $val, $operator, Boolean::AND);
        return $this;
    }

    /** @inheritdoc */
    public function whereOr(string $col, mixed $val, WhereOperator $operator = WhereOperator::EQ): self {
        $this->where->add($col, $val, $operator, Boolean::OR);
        return $this;
    }

    /** @inheritdoc */
    public function whereById(int $id): self {
        $this->where->whereById($id);
        return $this;
    }

    /** @inheritdoc */
    public function whereIsActive(bool $flag): self {
        $this->where->whereIsActive($flag);
        return $this;
    }

    /** @inheritdoc */
    public function group(Boolean $boolean = Boolean::AND): self {
        $this->where->group($boolean);
        return $this;
    }

    /** @inheritdoc */
    public function groupEnd(): self {
        $this->where->groupEnd();
        return $this;
    }

    /** @inheritdoc */
    public function andGroup(): self {
        $this->where->andGroup();
        return $this;
    }

    /** @inheritdoc */
    public function orGroup(): self {
        $this->where->orGroup();
        return $this;
    }

    /** @inheritdoc */
    public function join(string|array $table, string $first, string $second, JoinOperator $operator = JoinOperator::EQ, JoinType $type = JoinType::INNER): self {
        $this->join->add($table, $first, $second, $operator, $type);
        return $this;
    }
    /**
     * Добавляет INNER JOIN.
     *
     * @param string|array $table Имя таблицы или [table, alias]
     * @param string $first Левая колонка
     * @param string $second Правая колонка
     * @return self
     */
    public function innerJoin(string|array $table, string $first, string $second): self {
        return $this->join($table, $first, $second, JoinOperator::EQ, JoinType::INNER);
    }

    /**
     * Добавляет LEFT JOIN.
     *
     * @param string|array $table Имя таблицы или [table, alias]
     * @param string $first Левая колонка
     * @param string $second Правая колонка
     * @return self
     */
    public function leftJoin(string|array $table, string $first, string $second): self {
        return $this->join($table, $first, $second, JoinOperator::EQ, JoinType::LEFT);
    }

    /**
     * Добавляет RIGHT JOIN.
     *
     * @param string|array $table Имя таблицы или [table, alias]
     * @param string $first Левая колонка
     * @param string $second Правая колонка
     * @return self
     */
    public function rightJoin(string|array $table, string $first, string $second): self {
        return $this->join($table, $first, $second, JoinOperator::EQ, JoinType::RIGHT);
    }

    /**
     * Добавляет FULL JOIN.
     *
     * @param string|array $table Имя таблицы или [table, alias]
     * @param string $first Левая колонка
     * @param string $second Правая колонка
     * @return self
     */
    public function fullJoin(string|array $table, string $first, string $second): self {
        return $this->join($table, $first, $second, JoinOperator::EQ, JoinType::FULL);
    }

    /** @inheritdoc */
    public function groupBy(string $column): self {
        $this->groupBy->add($column);
        return $this;
    }

    /** @inheritdoc */
    public function having(string $condition, array $params = [], Boolean $boolean = Boolean::AND): self {
        $this->having->add($condition, $params, $boolean);
        return $this;
    }

    /** @inheritdoc */
    public function orderBy(string|array $column, OrderDirection $direction = OrderDirection::ASC): self {
        $this->orderBy->set($column, $direction);
        return $this;
    }

    /** @inheritdoc */
    public function limit(int $count): self {
        $this->limit->set($count);
        return $this;
    }

    /** @inheritdoc */
    public function offset(int $count): self {
        $this->offset->set($count);
        return $this;
    }

    /**
     * Устанавливает пагинацию (LIMIT + OFFSET).
     *
     * @param int $page Номер страницы (начиная с 1)
     * @param int $perPage Количество строк на страницу
     * @return array
     * @throws InvalidArgumentException Если значения некорректны
     */
    public function paginate(int $page, int $perPage): array {
        if ($page < 1) {
            throw new InvalidArgumentException("Page must be >= 1");
        }
        if ($perPage < 1) {
            throw new InvalidArgumentException("PerPage must be >= 1");
        }
        $offset = ($page - 1) * $perPage;
        $this->limit->set($perPage);
        $this->offset->set($offset);
        return $this->all();
    }

    /**
     * Возвращает все строки.
     *
     * @return array Массив строк
     */
    public function all(): array {
        $stmt = Query::run(DB::getToSql()->getSql(), DB::getToSql()->getParams());
        return $stmt ? $stmt->fetchAll() : [];
    }

    /**
     * Возвращает первую строку.
     *
     * @return array|null Первая строка или null
     */
    public function first(): ?array {
        $sql = DB::getToSql()->getSql() . " LIMIT 1";
        $stmt = Query::run($sql, DB::getToSql()->getParams());
        return $stmt ? $stmt->fetch() : null;
    }

    /**
     * Возвращает SQL‑строку с параметрами (для отладки).
     *
     * @return string SQL‑код
     */
    public function toSql(): string {
        return DB::getToSql()->getSqlStr();
    }
}
