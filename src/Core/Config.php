<?php

namespace App\Core;

/**
 * Class Config
 *
 * Утилита для загрузки конфигурационных файлов.
 */
class Config
{
    public static array $config = [];

    public static function init()
    {
        self::$config = self::load(BASE_PATH . '/config/app.php');
    }
    /**
     * Загружает конфиг из файла.
     *
     * @param string $path
     * @return array
     */
    public static function load(string $path): array
    {
        if (!file_exists($path)) {
            throw new \RuntimeException("Config file not found: {$path}");
        }

        return require $path;
    }
    public static function set(array $config): void
    {
        self::$config = $config;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::$config[$key] ?? $default;
    }
}
