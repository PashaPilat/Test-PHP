<?php
namespace App\DB\Contracts;

use PDOStatement;

interface QueryContract {
    /**
     * Выполняет SQL‑запрос с параметрами.
     *
     * @param string $sql    SQL‑строка
     * @param array  $params Параметры для подготовленного запроса
     * @return PDOStatement Результат выполнения запроса
     */
    public static function run(string $sql, array $params = []): PDOStatement;

    /**
     * Выполняет запрос и возвращает все строки.
     *
     * @param string $sql    SQL‑строка
     * @param array  $params Параметры для подготовленного запроса
     * @return array Массив строк результата
     */
    public static function fetchAll(string $sql, array $params = []): array;

    /**
     * Выполняет запрос и возвращает одну строку.
     *
     * @param string $sql    SQL‑строка
     * @param array  $params Параметры для подготовленного запроса
     * @return array|null Ассоциативный массив строки или null, если данных нет
     */
    public static function fetchOne(string $sql, array $params = []): ?array;

    /**
     * Выполняет запрос без выборки и возвращает количество затронутых строк.
     *
     * @param string $sql    SQL‑строка
     * @param array  $params Параметры для подготовленного запроса
     * @return int Количество затронутых строк
     */
    public static function execute(string $sql, array $params = []): int;
}
