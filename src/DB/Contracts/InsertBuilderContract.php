<?php
namespace App\DB\Contracts;

/**
 * Контракт для билдера INSERT‑запросов.
 * Определяет методы для указания таблицы, генерации SQL и выполнения запроса.
 */
interface InsertBuilderContract {
    /**
     * Указывает таблицу для вставки.
     *
     * @param string $table Имя таблицы
     * @return self
     */
    public function into(string $table): self;

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
     * Выполняет запрос INSERT.
     *
     * @return int|false ID вставленной строки или false при ошибке
     */
    public function exec(): int|false;
}
