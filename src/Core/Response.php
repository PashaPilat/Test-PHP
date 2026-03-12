<?php

namespace App\Core;

/**
 * Class Response
 *
 * Управляет HTTP ответами приложения.
 *
 * Предоставляет методы:
 * - json()  — отправка JSON ответа
 * - error() — отправка JSON ошибки
 * - html()  — отправка HTML
 * - text()  — отправка текстового ответа
 *
 * Используется контроллерами для формирования ответа клиенту.
 * Добавлены CORS-заголовки для поддержки AJAX-запросов
 * с фронтенда (например, http://localhost:3000).
 */
class Response
{
    /**
     * Отправляет JSON ответ.
     *
     * Устанавливает HTTP статус и заголовок Content-Type,
     * кодирует массив в JSON и завершает выполнение скрипта.
     *
     * @param array $data   Данные для отправки
     * @param int   $status HTTP статус код
     *
     * @return void
     */
    public static function json(array $data, int $status = 200): void
    {
        self::send(
            json_encode(
                $data,
                JSON_UNESCAPED_UNICODE |
                    JSON_UNESCAPED_SLASHES |
                    JSON_THROW_ON_ERROR
            ),
            'application/json',
            $status
        );
    }

    /**
     * Отправляет JSON ошибку.
     *
     * @param string $message Сообщение ошибки
     * @param int    $status  HTTP статус
     *
     * @return void
     */
    public static function error(string $message, int $status = 400): void
    {
        self::json([
            'success' => false,
            'error' => $message
        ], $status);
    }

    /**
     * Отправляет HTML ответ.
     *
     * @param string $content HTML содержимое
     * @param int    $status  HTTP статус
     *
     * @return void
     */
    public static function html(string $content, int $status = 200): void
    {
        self::send($content, 'text/html', $status);
    }

    /**
     * Отправляет текстовый ответ.
     *
     * @param string $content Текст
     * @param int    $status  HTTP статус
     *
     * @return void
     */
    public static function text(string $content, int $status = 200): void
    {
        self::send($content, 'text/plain', $status);
    }

    /**
     * Универсальный отправитель
     * 
     * Добавляет CORS-заголовки, выставляет Content-Type,
     * HTTP статус и выводит содержимое.
     * 
     * @param string $content Текст
     * @param string type  HTTP тип контента
     * @param int    $status  HTTP статус
     *
     * @return void
     */
    protected static function send(string $content, string $type, int $status): void
    {
        http_response_code($status);

        // Основные заголовки
        header("Content-Type: {$type}; charset=utf-8");
        // Формируем origin из конфигурации
        $port = \App\Core\Config::get('port_http', false);
        $origin = $port ? "http://localhost:{$port}" : "http://localhost";
        // CORS-заголовки
        header("Access-Control-Allow-Origin: {$origin}");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        // Preflight-запросы
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        echo $content;
        exit;
    }
}
