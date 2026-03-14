<?php

namespace App\Core;

/**
 * Class View
 *
 * Отвечает за сборку страницы:
 * - подключает layout (header, footer);
 * - вставляет контентный блок;
 * - подключает стили и скрипты.
 */
class View
{
    /** @var array Глобальные данные для всех шаблонов */
    protected array $shared = [];

    /** @var array|null Кэш mix-manifest.json */
    protected static ?array $manifest = null;
    /**  @var string Базовый путь views */
    protected static string $basePath = BASE_PATH . '/views/';

    /**
     * Рендерит страницу через layout.
     *
     * @param string $template Путь к шаблону (например catalog/index)
     * @param array  $data     Данные для шаблона
     * @param string $layoutFile Имя файла (например 'layout.php')
     * @return void
     */
    public function render(string $template, array $data = [], string $layoutFile = "Defaultlayout"): void
    {
        if (!self::exists($template)) {
            throw new \RuntimeException("View not found: {$template}");
        }
        $data = array_merge($this->shared, $data);
        extract($data, EXTR_SKIP);
        $contentFile = self::$basePath . $template . '.php';
        include self::$basePath . $layoutFile .'.php';
    }

    /**
     * Рендерит шаблон и возвращает HTML строку.
     *
     * Используется для AJAX ответов.
     *
     * @param string $template Путь к шаблону
     * @param array  $data     Данные шаблона
     *
     * @return string
     */
    public static function renderPartial(string $template, array $data = []): string
    {
        if (!self::exists($template)) {
            throw new \RuntimeException("View not found: {$template}");
        }
        extract($data, EXTR_SKIP);
        ob_start();
        include self::$basePath . $template . '.php';
        return ob_get_clean();
    }

    /**
     * Возвращает путь к ассету с версией из mix-manifest.json
     *
     * @param string $path Путь ассета
     *
     * @return string
     */
    public static function mix(string $path): string
    {
        if (self::$manifest === null) {

            $manifestPath = BASE_PATH . '/mix-manifest.json';

            if (!is_file($manifestPath)) {
                throw new \RuntimeException("mix-manifest.json not found");
            }

            self::$manifest = json_decode(
                file_get_contents($manifestPath),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        }
        if (!isset(self::$manifest[$path])) {
            throw new \RuntimeException("Asset not found in manifest: {$path}");
        }
        return self::$manifest[$path];
    }
    /**
     * HTML escape helper
     */
    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    /**
     * Проверяет существование шаблона
     */
    public static function exists(string $template): bool
    {
        return is_file(self::$basePath . $template . '.php');
    }
}
