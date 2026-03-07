<?php
namespace App\DB\Delete;

use App\DB;
use App\DB\Query;
use App\DB\Tools\Where;

use App\DB\Tools\Enum\Boolean;
use App\DB\Tools\Enum\WhereOperator;
use App\DB\Contracts\DeleteBuilderContract;
use InvalidArgumentException;

/**
 * Класс для построения и выполнения DELETE‑запросов.
 * Поддерживает fluent‑интерфейс, условия WHERE и группы условий.
 */
class Builder implements DeleteBuilderContract {
    /** @var string|null Имя таблицы */
    private ?string $table = null;

    /** @var Where Объект для построения условий WHERE */
    private Where $where;

    /**
     * Создаёт билдер для DELETE‑запроса.
     */
    public function __construct() {
        $this->where = new Where();
        DB::getToSql()?->add('delete', "DELETE");
    }

    /**
     * Указывает таблицу для удаления.
     *
     * @param string $table Имя таблицы
     * @return self
     * @throws InvalidArgumentException Если имя таблицы пустое
     */
    public function from(string $table): self {
        if (empty($table)) {
            throw new InvalidArgumentException("Table name cannot be empty");
        }
        $this->table = $table;
        DB::getToSql()?->add('from', "FROM {$this->table}");
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
     */
    public function toSql(): string {
        return DB::getToSql()->getSql();
    }

    /**
     * Возвращает SQL‑строку с подставленными параметрами (для отладки).
     */
    public function toSqlStr(): string {
        return DB::getToSql()->getSqlStr();
    }

    /**
     * Выполняет запрос DELETE.
     *
     * @return int|false Количество удалённых строк или false при ошибке
     */
    public function exec(): int|false {
        $stmt = Query::run($this->toSql(), DB::getToSql()->getParams());
        return $stmt ? $stmt->rowCount() : false;
    }
}
