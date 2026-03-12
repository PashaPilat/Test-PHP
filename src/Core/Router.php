<?php

namespace App\Core;

/**
 * Class Router
 *
 * Лёгкий HTTP роутер приложения.
 *
 * Возможности:
 * - маршруты с параметрами (/catalog/{slug})
 * - optional параметры (/page/{id?})
 * - кастомные regex (/user/{id:\d+})
 * - GET / POST / PUT / DELETE
 * - автоматическая передача query и payload
 */
class Router
{
    /**
     * Список зарегистрированных маршрутов
     *
     * @var array
     */
    protected array $routes = [];

    /**
     * Скомпилированные маршруты (regex)
     *
     * @var array
     */
    protected array $compiled = [];

    /**
     * Router constructor.
     *
     * @param array $routes
     */
    public function __construct(array $routes)
    {
        $this->routes = $routes;
        $this->compileRoutes();
    }

    /**
     * Компилирует маршруты в regex
     *
     * Выполняется один раз при создании Router
     *
     * @return void
     */
    protected function compileRoutes(): void
    {
        foreach ($this->routes as $method => $routes) {
            foreach ($routes as $route => $handler) {
                $paramNames = [];
                $pattern = preg_replace_callback(
                    '/\{([a-zA-Z_][a-zA-Z0-9_]*)(\:([^}]+))?\}/',
                    function ($matches) use (&$paramNames) {
                        $name = $matches[1];
                        $regex = $matches[3] ?? '[^\/]+';
                        $paramNames[] = $name;
                        return '(' . $regex . ')';
                    },
                    $route
                );
                $pattern = '#^' . $pattern . '$#';
                $this->compiled[$method][] = [
                    'pattern' => $pattern,
                    'params' => $paramNames,
                    'handler' => $handler
                ];
            }
        }
    }

    /**
     * Диспетчеризация HTTP запроса
     *
     * @param string $uri
     * @param array $query
     * @param array $payload
     * @param string $method
     * @return void
     */
    public function dispatch(string $uri, array $query = [], array $payload = [], string $method = 'GET'): void
    {
        $method = strtoupper($method);
        $path = parse_url($uri, PHP_URL_PATH);

        if (!isset($this->compiled[$method])) {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        foreach ($this->compiled[$method] as $route) {
            if (preg_match($route['pattern'], $path, $matches)) {
                array_shift($matches);
                $params = [];
                foreach ($route['params'] as $index => $name) {
                    $params[$name] = $matches[$index] ?? null;
                }
                $params['query'] = $query;
                $params['payload'] = $payload;
                $this->callHandler($route['handler'], $params);
                return;
            }
        }

        http_response_code(404);
        echo "Not Found";
    }

    /**
     * Вызывает обработчик маршрута
     *
     * @param mixed $handler
     * @param array $params
     * @return void
     */
    protected function callHandler(mixed $handler, array $params): void
    {
        if (is_callable($handler)) {
            $handler($params);
            return;
        }

        if (is_array($handler) && count($handler) === 2) {

            [$class, $method] = $handler;

            if (!class_exists($class)) {
                http_response_code(500);
                echo "Controller {$class} not found";
                return;
            }

            $controller = new $class();

            if (!method_exists($controller, $method)) {
                http_response_code(500);
                echo "Method {$method} not found";
                return;
            }

            $controller->$method($params);
            return;
        }

        http_response_code(500);
        echo "Invalid route handler";
    }
}
