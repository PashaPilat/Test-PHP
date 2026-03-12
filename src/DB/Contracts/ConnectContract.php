<?php
namespace App\DB\Contracts;

use PDO;

interface ConnectContract {
    /**
     * Инициализирует подключение к базе данных.
     */
    public static function init(): void;

    /**
     * Возвращает активное подключение PDO.
     *
     * @return PDO Экземпляр PDO
     */
    public static function pdo(): PDO;

    /**
     * Проверяет, установлено ли соединение.
     *
     * @return bool true если соединение активно, иначе false
     */
    public static function isConnected(): bool;
}
