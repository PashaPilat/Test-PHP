<?php
namespace App\Core;

use App\Core\View;

/**
 * Class BaseController
 *
 * Базовый класс для всех контроллеров.
 * Предоставляет метод render() для отображения шаблонов.
 */
abstract class BaseController
{
    protected View $view;

    public function __construct()  {
        $this->view = new View();
    }
    /**
     * Рендерит страницу через View.
     *
     * @param string $template Путь к шаблону (например, 'catalog/index')
     * @param array  $data     Данные для передачи в шаблон
     * @param string $layoutFile Имя файла (например 'layout.php')
     * @return void
     */
    protected function render(string $template, array $data = [], string $layoutFile = "Defaultlayout"): void
    {
        $this->view->render($template, $data, $layoutFile);
    }
}
