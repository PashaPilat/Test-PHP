<?php

namespace App\Core;

/**
 * Class Logger
 *
 * Статический сервис для записи логов.
 * - Создаёт файлы вида logs_dd_mm_yyyy.log и error_dd_mm_yyyy.log.
 * - Записывает сообщения с временем и миллисекундами.
 * - Удаляет старые логи по настройке.
 */
class Logger
{
    /**
     * @var string Папка для логов
     */
    protected static string $baseDir;

    /**
     * @var int Количество дней хранения логов (0 = не удалять)
     */
    protected static int $daysToKeep;

    /**
     * Инициализация логгера.
     *
     * @param string $baseDir    Папка для логов
     * @param int    $daysToKeep Количество дней хранения логов
     * @return void
     */
    public static function init(string $baseDir, int $daysToKeep = 10): void
    {
        self::$baseDir = $baseDir;
        self::$daysToKeep = $daysToKeep;

        if (!is_dir(self::$baseDir)) {
            mkdir(self::$baseDir, 0777, true);
        }
    }

    /**
     * Записывает системное сообщение в logs_dd_mm_yyyy.log.
     *
     * @param string      $message Сообщение
     * @param string|null $context Дополнительный контекст
     * @return void
     */
    public static function info(string $message, ?string $context = null): void
    {
        self::write('logs', $message, $context);
    }

    /**
     * Записывает ошибки в файл error_dd_mm_yyyy.log.
     *
     * @param array $errors Массив ошибок по типам
     * @return void
     */
    public static function error(array $errors): void
    {
        if (empty($errors['Exception']) && empty($errors['Error'])) {
            return;
        }

        $date = date('d_m_Y');
        $file = self::$baseDir . "/error_{$date}.log";
        self::cleanupOldLogs('error');

        $micro = microtime(true);
        $time  = date('H:i:s', (int)$micro) . sprintf('.%03d', ($micro - floor($micro)) * 1000);

        $log = "==============================\n";
        $log .= "[{$time}] Ошибки за текущий запрос\n";

        $log .= "URI: " . ($_SERVER['REQUEST_URI'] ?? '-') . "\n";
        $log .= "METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? '-') . "\n";
        $log .= "IP: " . ($_SERVER['REMOTE_ADDR'] ?? '-') . "\n";
        $log .= "------------------------------\n";
        foreach ($errors as $type => $entries) {
            foreach ($entries as $entry) {
                $log .= "--- {$type} ---\n";
                $log .= $entry['message'] . "\n";
                if (!empty($entry['trace'])) {
                    $log .= $entry['trace'] . "\n";
                }
            }
        }

        $log .= "==============================\n";

        file_put_contents($file, $log, FILE_APPEND);
    }

    /**
     * Внутренний метод записи системных сообщений.
     *
     * @param string      $prefix  Префикс файла (logs)
     * @param string      $message Сообщение
     * @param string|null $context Дополнительный контекст
     * @return void
     */
    protected static function write(string $prefix, string $message, ?string $context = null): void
    {
        $date = date('d_m_Y');
        $file = self::$baseDir . "/{$prefix}_{$date}.log";
        self::cleanupOldLogs($prefix);

        $micro = microtime(true);
        $time  = date('H:i:s', (int)$micro) . sprintf('.%03d', ($micro - floor($micro)) * 1000);

        $log = "------------------------\n";
        $log .= "[{$time}] {$message}\n";
        if ($context) {
            $log .= "{$context}\n";
        }
        $log .= "------------------------\n";

        file_put_contents($file, $log, FILE_APPEND);
    }

    /**
     * Удаляет старые логи.
     *
     * @param string $prefix Префикс файла (logs или error)
     * @return void
     */
    protected static function cleanupOldLogs(string $prefix): void
    {
        if (self::$daysToKeep <= 0) {
            return;
        }

        $files = glob(self::$baseDir . "/{$prefix}_*.log");
        $threshold = time() - (self::$daysToKeep * 86400);

        foreach ($files as $file) {
            if (filemtime($file) < $threshold) {
                unlink($file);
            }
        }
    }
}
