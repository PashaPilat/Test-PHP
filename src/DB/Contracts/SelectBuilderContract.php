<?php
namespace App\DB\Contracts;

use App\DB\Tools\Enum\WhereOperator;
use App\DB\Tools\Enum\Boolean;
use App\DB\Tools\Enum\JoinOperator;
use App\DB\Tools\Enum\JoinType;
use App\DB\Tools\Enum\OrderDirection;

/**
 * Контракт для билдера SELECT‑запросов.
 * Определяет методы для указания колонок, таблиц, условий, соединений и выполнения запроса.
 */
interface SelectBuilderContract {
    /**
     * Указывает таблицу для выборки.
     *
     * @param string|array $table Имя таблицы или массив имён
     * @return self
     */
    public function from(string|array $table): self;

    /**
     * Добавляет условие WHERE (по умолчанию AND).
     *
     * @param string $col Имя колонки
     * @param mixed $val Значение
     * @param WhereOperator $operator Оператор сравнения
     * @return self
     */
    public function where(string $col, mixed $val, WhereOperator $operator = WhereOperator::EQ): self;

    /**
     * Добавляет условие WHERE с OR.
     *
     * @param string $col Имя колонки
     * @param mixed $val Значение
     * @param WhereOperator $operator Оператор сравнения
     * @return self
     */
    public function whereOr(string $col, mixed $val, WhereOperator $operator = WhereOperator::EQ): self;

    /**
     * Добавляет условие "id = ?".
     *
     * @param int $id Идентификатор
     * @return self
     */
    public function whereById(int $id): self;

    /**
     * Добавляет условие "status = 'active'" или "status != 'active'".
     *
     * @param bool $flag true для активных, false для неактивных
     * @return self
     */
    public function whereIsActive(bool $flag): self;

    /**
     * Начинает группу условий.
     *
     * @param Boolean $boolean Логический оператор для группы (AND/OR)
     * @return self
     */
    public function group(Boolean $boolean = Boolean::AND): self;

    /**
     * Завершает группу условий.
     *
     * @return self
     */
    public function groupEnd(): self;

    /**
     * Алиас для группы с AND.
     *
     * @return self
     */
    public function andGroup(): self;

    /**
     * Алиас для группы с OR.
     *
     * @return self
     */
    public function orGroup(): self;

    /**
     * Добавляет JOIN.
     *
     * @param string|array $table Имя таблицы или [table, alias]
     * @param string $first Левая колонка
     * @param string $second Правая колонка
     * @param JoinOperator $operator Оператор сравнения
     * @param JoinType $type Тип соединения (INNER/LEFT/RIGHT/FULL)
     * @return self
     */
    public function join(string|array $table, string $first, string $second, JoinOperator $operator = JoinOperator::EQ, JoinType $type = JoinType::INNER): self;

    /**
     * Добавляет INNER JOIN (сахар).
     *
     * @param string|array $table Имя таблицы или [table, alias]
     * @param string $first Левая колонка
     * @param string $second Правая колонка
     * @return self
     */
    public function innerJoin(string|array $table, string $first, string $second): self;

    /**
     * Добавляет LEFT JOIN (сахар).
     *
     * @param string|array $table Имя таблицы или [table, alias]
     * @param string $first Левая колонка
     * @param string $second Правая колонка
     * @return self
     */
    public function leftJoin(string|array $table, string $first, string $second): self;

    /**
     * Добавляет RIGHT JOIN (сахар).
     *
     * @param string|array $table Имя таблицы или [table, alias]
     * @param string $first Левая колонка
     * @param string $second Правая колонка
     * @return self
     */
    public function rightJoin(string|array $table, string $first, string $second): self;

    /**
     * Добавляет FULL JOIN (сахар).
     *
     * @param string|array $table Имя таблицы или [table, alias]
     * @param string $first Левая колонка
     * @param string $second Правая колонка
     * @return self
     */
    public function fullJoin(string|array $table, string $first, string $second): self;

    /**
     * Добавляет GROUP BY.
     *
     * @param string $column Имя колонки
     * @return self
     */
    public function groupBy(string $column): self;

    /**
     * Добавляет HAVING.
     *
     * @param string $condition Условие
     * @param array $params Параметры
     * @param Boolean $boolean Логическая связка
     * @return self
     */
    public function having(string $condition, array $params = [], Boolean $boolean = Boolean::AND): self;

    /**
     * Добавляет ORDER BY.
     *
     * @param string|array $column Имя колонки или массив пар
     * @param OrderDirection $direction ASC или DESC
     * @return self
     */
    public function orderBy(string|array $column, OrderDirection $direction = OrderDirection::ASC): self;

    /**
     * Добавляет LIMIT.
     *
     * @param int $count Количество строк
     * @return self
     */
    public function limit(int $count): self;

    /**
     * Добавляет OFFSET.
     *
     * @param int $count Смещение
     * @return self
     */
    public function offset(int $count): self;

    /**
     * Устанавливает пагинацию (LIMIT + OFFSET).
     *
     * @param int $page Номер страницы (начиная с 1)
     * @param int $perPage Количество строк на страницу
     * @return array
     */
    public function paginate(int $page, int $perPage): array;

    /**
     * Возвращает все строки.
     *
     * @return array Массив строк
     */
    public function all(): array;

    /**
     * Возвращает первую строку.
     *
     * @return array|null Первая строка или null
     */
    public function first(): ?array;

    /**
     * Возвращает SQL‑строку с параметрами (для отладки).
     *
     * @return string SQL‑код
     */
    public function toSql(): string;
}
