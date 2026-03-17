<?php
namespace App\DB\Contracts;

use PDOStatement;
use RuntimeException;

interface QueryContract {
    /**
     * Выполняет SQL‑запрос с параметрами и дополнительным контекстом.
     *
     * Используется для отладки и диагностики: при ошибке запроса
     * вызывается специализированный обработчик, которому передаются
     * SQL‑строка, параметры и сборочный стек билдера.
     *
     * @param string $sql          SQL‑строка
     * @param array  $params       Параметры для подготовленного запроса
     * @param array  $builderStack Сборочный стек билдера (для отладки)
     *
     * @return PDOStatement        Результат выполнения запроса
     *
     * @throws RuntimeException    Если выполнение запроса завершилось ошибкой
     */
    public static function run(string $sql, array $params = [], array $builderStack = []): PDOStatement;

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
