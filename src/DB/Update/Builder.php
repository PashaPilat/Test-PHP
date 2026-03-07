<?php
namespace App\DB\Update;

use App\DB;
use App\DB\Query;
use App\DB\Tools\Where;
use App\DB\Tools\Enum\WhereOperator;
use App\DB\Tools\Enum\Boolean;
use App\DB\Contracts\UpdateBuilderContract;

/**
 * Класс для построения и выполнения UPDATE‑запросов.
 * Поддерживает fluent‑интерфейс, условия WHERE и группы условий.
 */
class Builder implements UpdateBuilderContract {
    /** @var string Имя таблицы */
    private string $table;

    /** @var array Параметры для подготовленного запроса */
    private array $params = [];

    /** @var Where Объект для построения условий WHERE */
    private Where $where;

    /**
     * Создаёт билдер для UPDATE‑запроса.
     *
     * @param string $table Имя таблицы
     */
    public function __construct(string $table) {
        $this->table = $table;
        $this->where = new Where();
        DB::getToSql()?->add('update', "UPDATE {$this->table}");
    }

    /**
     * Устанавливает значения для обновления.
     *
     * @param array $values Ассоциативный массив колонка => значение
     * @return self
     */
    public function set(array $values): self {
        $setParts = [];
        foreach ($values as $col => $val) {
            $setParts[] = "{$col} = ?";
            $this->params[] = $val;
        }
        DB::getToSql()?->add('set', "SET " . implode(', ', $setParts), $this->params);
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

    /**
     * Возвращает SQL‑строку с плейсхолдерами (?).
     *
     * @return string SQL‑код
     */
    public function toSql(): string {
        return DB::getToSql()->getSql();
    }

    /**
     * Возвращает SQL‑строку с подставленными параметрами (для отладки).
     *
     * @return string SQL‑код с параметрами
     */
    public function toSqlStr(): string {
        return DB::getToSql()->getSqlStr();
    }

    /**
     * Выполняет запрос UPDATE.
     *
     * @return int|false Количество затронутых строк или false при ошибке
     */
    public function exec(): int|false {
        $stmt = Query::run($this->toSql(), DB::getToSql()->getParams());
        return $stmt ? $stmt->rowCount() : false;
    }
}
