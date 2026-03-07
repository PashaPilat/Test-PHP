<?php
namespace App\DB\Contracts;

use App\DB\Tools\Enum\WhereOperator;
use App\DB\Tools\Enum\Boolean;

/**
 * Контракт для построения условий WHERE.
 */
interface WhereContract {
    /**
     * Добавляет условие в WHERE.
     *
     * @param string $col Имя колонки
     * @param mixed $val Значение
     * @param WhereOperator $operator Оператор сравнения
     * @param Boolean $boolean Логический оператор (AND/OR)
     */
    public function add(
        string $col,
        mixed $val,
        WhereOperator $operator = WhereOperator::EQ,
        Boolean $boolean = Boolean::AND
    ): void;

    /**
     * Начинает группу условий.
     *
     * @param Boolean $boolean Логический оператор для группы
     */
    public function group(Boolean $boolean = Boolean::AND): void;

    /**
     * Завершает группу условий.
     */
    public function groupEnd(): void;

    /**
     * Удобный алиас для группы с AND.
     */
    public function andGroup(): void;

    /**
     * Удобный алиас для группы с OR.
     */
    public function orGroup(): void;

    /**
     * Добавляет условие "status = 'active'" или "status != 'active'".
     *
     * @param bool $flag true для активных, false для неактивных
     */
    public function whereIsActive(bool $flag): void;

    /**
     * Добавляет условие "id = ?".
     *
     * @param int $id Идентификатор
     */
    public function whereById(int $id): void;

    /**
     * Возвращает SQL‑строку WHERE.
     */
    public function getSql(): string;

    /**
     * Возвращает параметры для подготовленного запроса.
     */
    public function getParams(): array;
}
