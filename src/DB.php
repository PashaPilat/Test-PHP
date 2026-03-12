<?php
namespace App;

use App\DB\Connect;
use App\DB\Query;
use App\DB\Select\Builder as Select;
use App\DB\Insert\Builder as Insert;
use App\DB\Update\Builder as Update;
use App\DB\Delete\Builder as Delete;
use App\DB\Tools\ToSql;
//use App\DB\Tools\Collection;

use App\DB\Contracts\DBContract;

class DB implements DBContract {
    /** @var ToSql|null Текущий объект построителя SQL */
    protected static ?ToSql $toSql = null;

    /**
     * Инициализирует подключение к базе данных.
     */
    public static function connect(): void {
        Connect::init();
    }

    /**
     * Создаёт SELECT‑запрос.
     *
     * @param array|string $columns Список колонок или строка ('*' по умолчанию)
     * @return Select Построитель SELECT‑запроса
     */
    public static function select(array|string $columns = ['*']): Select {
        self::$toSql = new ToSql();
        return new Select($columns);
    }

    /**
     * Создаёт INSERT‑запрос.
     *
     * @param array $values Ассоциативный массив значений для вставки
     * @return Insert Построитель INSERT‑запроса
     */
    public static function insert(array $values): Insert {
        self::$toSql = new ToSql();
        return new Insert($values);
    }

    /**
     * Создаёт UPDATE‑запрос.
     *
     * @param string $table Имя таблицы
     * @return Update Построитель UPDATE‑запроса
     */
    public static function update(string $table): Update {
        self::$toSql = new ToSql();
        return new Update($table);
    }

    /**
     * Создаёт DELETE‑запрос.
     *
     * @return Delete Построитель DELETE‑запроса
     */
    public static function delete(): Delete {
        self::$toSql = new ToSql();
        return new Delete();
    }

    /**
     * Возвращает текущий объект ToSql.
     *
     * @return ToSql|null Построитель SQL или null, если не инициализирован
     */
    public static function getToSql(): ?ToSql {
        return self::$toSql;
    }

    /**
     * Возвращает SQL‑строку для текущего запроса.
     *
     * @return string SQL‑код запроса
     */
    public static function toSql(): string {
        return self::$toSql?->getSql() ?? '';
    }

    /**
     * Выполняет произвольный SQL‑запрос.
     *
     * @param string $sql    SQL‑строка
     * @param array  $params Параметры для подготовленного запроса
     * @return \PDOStatement|null Результат выполнения запроса или null при ошибке
     */
    public static function query(string $sql, array $params = []): ?\PDOStatement {
        return Query::run($sql, $params);
    }
}
