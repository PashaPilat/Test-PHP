<?php

namespace App\DB;

use PDOException;
use PDOStatement;
use RuntimeException;
use App\DB\Contracts\QueryContract;

class Query implements QueryContract
{
    /**
     * Выполняет SQL‑запрос с параметрами.
     *
     * @param string $sql    SQL‑строка
     * @param array  $params Параметры для подготовленного запроса
     * @return PDOStatement Результат выполнения запроса
     * @throws RuntimeException Если выполнение запроса завершилось ошибкой
     */
    public static function run(string $sql, array $params = []): PDOStatement
    {
        try {
            $stmt = Connect::pdo()->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new RuntimeException("DB query error: " . $e->getMessage() . " in query: {$sql} with params: " . json_encode($params), 0, $e);
        }
    }

    /**
     * Выполняет запрос и возвращает все строки.
     *
     * @param string $sql    SQL‑строка
     * @param array  $params Параметры для подготовленного запроса
     * @return array Массив строк результата
     */
    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::run($sql, $params)->fetchAll();
    }

    /**
     * Выполняет запрос и возвращает одну строку.
     *
     * @param string $sql    SQL‑строка
     * @param array  $params Параметры для подготовленного запроса
     * @return array|null Ассоциативный массив строки или null, если данных нет
     */
    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = self::run($sql, $params);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    /**
     * Выполняет запрос без выборки и возвращает количество затронутых строк.
     *
     * @param string $sql    SQL‑строка
     * @param array  $params Параметры для подготовленного запроса
     * @return int Количество затронутых строк
     */
    public static function execute(string $sql, array $params = []): int
    {
        $stmt = self::run($sql, $params);
        return $stmt->rowCount();
    }
}
