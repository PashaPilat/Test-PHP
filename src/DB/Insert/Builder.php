<?php
namespace App\DB\Insert;

use App\DB;
use App\DB\Query;
use App\DB\Contracts\InsertBuilderContract;
use InvalidArgumentException;

/**
 * Класс для построения и выполнения INSERT‑запросов.
 * Поддерживает вставку одной строки или нескольких строк.
 */
class Builder implements InsertBuilderContract {
    /** @var string|null Имя таблицы */
    private ?string $table = null;

    /** @var array Список колонок */
    private array $columns = [];

    /** @var array Значения для вставки */
    private array $values = [];

    /**
     * Создаёт билдер для INSERT‑запроса.
     *
     * @param array $values Ассоциативный массив (одна строка) или массив массивов (несколько строк)
     * @throws InvalidArgumentException Если массив пустой или некорректный
     */
    public function __construct(array $values) {
        if (empty($values)) {
            throw new InvalidArgumentException("Insert values cannot be empty");
        }

        if (isset($values[0]) && is_array($values[0])) {
            // массив строк
            if (empty($values[0])) {
                throw new InvalidArgumentException("Insert row cannot be empty");
            }
            $this->columns = array_keys($values[0]);
            $this->values = $values;
        } else {
            // одна строка
            $this->columns = array_keys($values);
            $this->values = [$values];
        }
    }

    /**
     * Указывает таблицу для вставки.
     *
     * @param string $table Имя таблицы
     * @return self
     * @throws InvalidArgumentException Если имя таблицы пустое
     */
    public function into(string $table): self {
        if (empty($table)) {
            throw new InvalidArgumentException("Table name cannot be empty");
        }
        if (empty($this->columns)) {
            throw new InvalidArgumentException("No columns defined for insert");
        }

        $this->table = $table;
        $cols = implode(',', $this->columns);

        $placeholdersRows = [];
        $params = [];
        foreach ($this->values as $row) {
            if (count($row) !== count($this->columns)) {
                throw new InvalidArgumentException("Row values count does not match columns count");
            }
            $placeholdersRows[] = '(' . implode(',', array_fill(0, count($row), '?')) . ')';
            $params = array_merge($params, array_values($row));
        }

        $sql = "INSERT INTO {$this->table} ({$cols}) VALUES " . implode(',', $placeholdersRows);
        DB::getToSql()?->add('insert', $sql, $params);
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
     * Выполняет запрос INSERT.
     *
     * @return int|false ID вставленной строки или false при ошибке
     */
    public function exec(): int|false {
        $stmt = Query::run($this->toSql(), DB::getToSql()->getParams());
        return $stmt ? (int) \App\DB\Connect::pdo()->lastInsertId() : false;
    }
}
