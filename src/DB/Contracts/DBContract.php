<?php
namespace App\DB\Contracts;

use App\DB\Select\Builder as Select;
use App\DB\Insert\Builder as Insert;
use App\DB\Update\Builder as Update;
use App\DB\Delete\Builder as Delete;
use App\DB\Tools\ToSql;

interface DBContract {
    /**
     * Инициализирует подключение к базе данных.
     *
     * @param array $config Конфигурация подключения (dsn, user, password и т.д.)
     */
    public static function connect(array $config): void;

    /**
     * Создаёт SELECT‑запрос.
     *
     * @param array|string $columns Список колонок или строка ('*' по умолчанию)
     * @return Select Построитель SELECT‑запроса
     */
    public static function select(array|string $columns = ['*']): Select;

    /**
     * Создаёт INSERT‑запрос.
     *
     * @param array $values Ассоциативный массив значений для вставки
     * @return Insert Построитель INSERT‑запроса
     */
    public static function insert(array $values): Insert;

    /**
     * Создаёт UPDATE‑запрос.
     *
     * @param string $table Имя таблицы
     * @return Update Построитель UPDATE‑запроса
     */
    public static function update(string $table): Update;

    /**
     * Создаёт DELETE‑запрос.
     *
     * @return Delete Построитель DELETE‑запроса
     */
    public static function delete(): Delete;

    /**
     * Возвращает текущий объект ToSql.
     *
     * @return ToSql|null Построитель SQL или null, если не инициализирован
     */
    public static function getToSql(): ?ToSql;

    /**
     * Возвращает SQL‑строку для текущего запроса.
     *
     * @return string SQL‑код запроса
     */
    public static function toSql(): string;

    /**
     * Выполняет произвольный SQL‑запрос.
     *
     * @param string $sql    SQL‑строка
     * @param array  $params Параметры для подготовленного запроса
     * @return \PDOStatement|null Результат выполнения запроса или null при ошибке
     */
    public static function query(string $sql, array $params = []): ?\PDOStatement;
}
