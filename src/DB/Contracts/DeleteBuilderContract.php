<?php
namespace App\DB\Contracts;

use App\DB\Tools\Enum\WhereOperator;
use App\DB\Tools\Enum\Boolean;

/**
 * Контракт для билдера DELETE‑запросов.
 * Определяет методы для указания таблицы, добавления условий и выполнения запроса.
 */
interface DeleteBuilderContract {
    /**
     * Указывает таблицу для удаления.
     *
     * @param string $table Имя таблицы
     * @return self
     */
    public function from(string $table): self;

    /**
     * Добавляет условие WHERE (по умолчанию AND).
     *
     * @param string $col      Имя колонки
     * @param mixed  $val      Значение
     * @param string $operator Оператор сравнения
     * @return self
     */
    
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
     * @paramBoolean $boolean Логический оператор для группы (AND/OR)
     * @return self
     */
    public function group(Boolean $boolean =Boolean::AND): self;

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
     * Возвращает SQL‑строку с плейсхолдерами (?).
     *
     * @return string SQL‑код
     */
    public function toSql(): string;

    /**
     * Возвращает SQL‑строку с подставленными параметрами (для отладки).
     *
     * @return string SQL‑код с параметрами
     */
    public function toSqlStr(): string;

    /**
     * Выполняет запрос DELETE.
     *
     * @return int|false Количество удалённых строк или false при ошибке
     */
    public function exec(): int|false;
}
