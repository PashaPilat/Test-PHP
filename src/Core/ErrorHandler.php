<?php

namespace App\Core;

/**
 * Class ErrorHandler
 *
 * Статический обработчик ошибок.
 * - Буферизует ошибки по типам: Exception и Error.
 * - Не пишет в лог сразу, а только добавляет в массив.
 * - Записывает все накопленные ошибки в лог один раз — при завершении работы или вызове flush().
 */
class ErrorHandler
{
    /**
     * @var bool Режим отладки
     */
    protected static bool $debug;
    protected static bool $flushed = false;

    /**
     * @var array Буфер ошибок по типам
     */
    protected static array $errors = [
        'Exception' => [],
        'Error'     => []
    ];

    /**
     * Инициализация обработчика ошибок.
     *
     * @return void
     */
    public static function init(): void
    {
        self::$debug = Config::get('debug', false);
        Logger::init(BASE_PATH . '/storage/logs', Config::get('log_days', false));

        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    /**
     * Обработка непойманных исключений.
     *
     * @param \Throwable $e Исключение
     * @return void
     */
    public static function handleException(\Throwable $e): void
    {
        self::$errors['Exception'][] = [
            'message' => $e->getMessage(),
            'trace'   => $e->getTraceAsString()
        ];

        http_response_code(500);
        self::$debug ? self::renderDebug($e) : self::renderProduction();
        exit;
    }

    /**
     * Обработка ошибок PHP (Notice, Warning).
     *
     * @param int    $errno   Код ошибки
     * @param string $errstr  Сообщение
     * @param string $errfile Файл
     * @param int    $errline Строка
     * @return void
     */
    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): void
    {
        if (!(error_reporting() & $errno))  return;
        self::$errors['Error'][] = [
            'message' => "[Error {$errno}] {$errstr} in {$errfile}:{$errline}"
        ];
    }

    /**
     * Обработка завершения скрипта.
     *
     * @return void
     */
    public static function handleShutdown(): void
    {
        self::flush();
    }

    /**
     * Записывает накопленные ошибки в лог и очищает массив.
     *
     * @return void
     */
    public static function flush(): void
    {
        if (self::$flushed) {
            return;
        }
        if (empty(self::$errors['Exception']) && empty(self::$errors['Error'])) {
            return;
        }

        Logger::error(self::$errors);

        self::$errors = [
            'Exception' => [],
            'Error'     => []
        ];
    }

    /**
     * Красивый вывод ошибки в debug режиме.
     *
     * @param \Throwable $e Исключение
     * @return void
     */
    protected static function renderDebug(\Throwable $e): void
    {
        $message = $e->getMessage();
        $trace   = $e->getTraceAsString();
        $file    = $e->getFile();
        $line    = $e->getLine();
        $traceArray = $e->getTrace();

        $snippet = self::getCodeSnippet($file, $line);

        $viewFile = BASE_PATH . '/views/error/error.php';

        include $viewFile;
    }

    /**
     * Вывод ошибки в production режиме.
     *
     * @return void
     */
    protected static function renderProduction(): void
    {
        $viewFile = BASE_PATH . '/views/error/error_production.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "Internal Server Error";
        }
    }
    protected static function getCodeSnippet(string $file, int $line, int $padding = 6): array
    {
        if (!is_file($file)) {
            return [];
        }

        $lines = file($file);

        $start = max($line - $padding - 1, 0);
        $length = $padding * 2 + 1;

        return array_slice($lines, $start, $length, true);
    }
}
