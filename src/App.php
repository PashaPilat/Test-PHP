<?php

namespace App;

use App\DB;
use App\Core\Config;
use App\Core\ErrorHandler;
use App\Core\Logger;
use App\Core\Router;

/**
 * Class App
 *
 * Главная точка входа приложения:
 * - загружает конфиги;
 * - инициализирует ErrorHandler и Logger;
 * - создаёт Router;
 * - передаёт управление роутеру для обработки запроса;
 * - выполняет завершающие задачи (flush логов, запись времени выполнения).
 */
class App
{
    /**
     * @var float Время старта запроса (в секундах с микросекундами)
     */
    private float $startTime;

    /**
     * @var Router Роутер приложения
     */
    private Router $router;

    /**
     * App constructor.
     *
     * Загружает конфигурацию, инициализирует ErrorHandler и Router.
     */
    public function __construct()
    {
        $this->startTime = microtime(true);

        // Загружаем глобальные настройки
        Config::init();

        // Инициализация глобального обработчика ошибок
        ErrorHandler::init();
        DB::connect();
        // Инициализация роутера
        $routes = Config::load(BASE_PATH . '/config/root.php');
        $this->router = new Router($routes);
    }

    /**
     * Запуск приложения.
     *
     * Передаёт управление роутеру для обработки HTTP-запроса.
     *
     * @return void
     */
    public function run(): void
    {
        $uri     = $_SERVER['REQUEST_URI'] ?? '/';
        $method  = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $query   = $_GET ?? [];
        $payload = $_POST ?? [];

        $this->router->dispatch($uri, $query, $payload, $method);
        $this->finish();
    }

    /**
     * Завершающие задачи приложения.
     * - Сбрасывает накопленные ошибки в лог.
     * - Записывает время выполнения запроса.
     *
     * @return void
     */
    public function finish(): void
    {
        ErrorHandler::flush();

        if (Config::get('log_time_execution', false)) {
            $executionTime = microtime(true) - $this->startTime;

            Logger::info(
                "Запрос: " . ($_SERVER['REQUEST_URI'] ?? '/'),
                "Время выполнения: " . number_format($executionTime, 3) . " сек."
            );
        }
    }
}
