<?php
namespace App\DB;

use PDO;
use PDOException;
use RuntimeException;
use App\DB\Contracts\ConnectContract;

class Connect implements ConnectContract {
    /** @var PDO|null Экземпляр PDO */
    private static ?PDO $pdo = null;

    /**
     * Инициализирует подключение к базе данных.
     * Если соединение уже установлено, повторная инициализация не выполняется.
     *
     * @param array $config Конфигурация подключения:
     *                      - dsn: строка DSN (например "mysql:host=localhost;dbname=test")
     *                      - user: имя пользователя
     *                      - pass: пароль
     *
     * @throws RuntimeException Если подключение не удалось
     */
    public static function init(array $config): void {
        if (self::$pdo === null) {
            try {
                if (!isset($config['dsn'], $config['user'], $config['pass'])) {
                    throw new RuntimeException("Invalid DB config: missing dsn/user/pass");
                }

                self::$pdo = new PDO($config['dsn'], $config['user'], $config['pass']);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                throw new RuntimeException("DB connection failed: " . $e->getMessage(), 0, $e);
            }
        }
    }

    /**
     * Возвращает активное подключение PDO.
     *
     * @return PDO Экземпляр PDO
     * @throws RuntimeException Если соединение не установлено
     */
    public static function pdo(): PDO {
        if (self::$pdo === null) {
            throw new RuntimeException("No active DB connection. Call Connect::init() first.");
        }
        return self::$pdo;
    }

    /**
     * Проверяет, установлено ли соединение.
     *
     * @return bool true если соединение активно, иначе false
     */
    public static function isConnected(): bool {
        return self::$pdo instanceof PDO;
    }
}
