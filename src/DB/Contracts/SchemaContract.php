<?php
namespace App\DB\Contracts;

interface SchemaContract {
    /**
     * Создание таблицы (только колонки).
     */
    public static function create(string $table, callable $callback): void;

    /**
     * Добавление/синхронизация индексов и внешних ключей.
     */
    public static function table(string $table, callable $callback): void;

    /**
     * Удаляет таблицу, если она существует.
     * @return bool
     */
    public static function dropIfExists(string $table): bool;

    /**
     * Создаёт триггер.
     *
     * @param string $name Имя триггера
     * @param string $timing BEFORE|AFTER
     * @param string $event INSERT|UPDATE|DELETE
     * @param string $table Таблица
     * @param string $body Тело триггера (SQL)
     */
    public static function trigger(string $name, string $timing, string $event, string $table, string $body): void;

    /**
     * Удаляет триггер, если он существует.
     */
    public static function dropTrigger(string $name): void;

    /**
     * Создаёт триггеры для автоматического обновления временных меток.
     */
    public static function timestampTriggers(string $table): void;

    /**
     * Создаёт представление.
     */
    public static function view(string $name, string $selectSql): void;

    /**
     * Удаляет представление, если оно существует.
     */
    public static function dropView(string $name): void;
}
