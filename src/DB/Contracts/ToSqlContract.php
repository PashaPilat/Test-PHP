<?php
namespace App\DB\Contracts;

interface ToSqlContract {
    /**
     * Допустимые части SQL‑запроса.
     */
    public const ALLOWED_PARTS = [
        'select','insert','update','delete',
        'from','set','values',
        'join','where','group','having',
        'order','limit','offset'
    ];

    /**
     * Добавляет часть SQL‑запроса.
     *
     * @param string $key   Ключ (например select, from, where)
     * @param string $sql   SQL‑фрагмент
     * @param array  $params Параметры для подготовленного запроса
     */
    public function add(string $key, string $sql, array $params = []): void;

    /**
     * Собирает SQL‑запрос из частей.
     *
     * @throws \RuntimeException Если запрос некорректен
     */
    public function build(): void;

    /**
     * Возвращает SQL‑строку с плейсхолдерами (?).
     *
     * @return string SQL‑код
     */
    public function getSql(): string;

    /**
     * Возвращает SQL‑строку с подставленными параметрами (для отладки).
     *
     * @return string SQL‑код с параметрами
     */
    public function getSqlStr(): string;

    /**
     * Возвращает массив параметров для подготовленного запроса.
     *
     * @return array Параметры
     */
    public function getParams(): array;

    /**
     * Сбрасывает все части и параметры.
     */
    public function reset(): void;

    /**
     * Проверяет, добавлена ли часть запроса.
     *
     * @param string $key Ключ
     * @return bool true если часть есть
     */
    public function has(string $key): bool;

    /**
     * Удаляет часть запроса.
     *
     * @param string $key Ключ
     */
    public function remove(string $key): void;

    /**
     * Возвращает структуру частей для отладки.
     *
     * @return array Массив частей
     */
    public function debug(): array;

    /**
     * Объединяет текущий билдер с другим.
     *
     * @param ToSqlContract $other Другой билдер
     */
    public function merge(ToSqlContract $other): void;

    /**
     * Проверяет корректность собранного запроса.
     *
     * @throws \RuntimeException Если запрос некорректен
     */
    public function validate(): void;
}
