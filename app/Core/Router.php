<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array|callable $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, array|callable $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, array|callable $handler): void
    {
        $normalized = '/' . trim($path, '/');
        $this->routes[] = [
            'method'  => $method,
            'path'    => $normalized,
            'pattern' => $this->convertPathToRegex($normalized),
            'handler' => $handler,
        ];
    }

    private function convertPathToRegex(string $path): string
    {
        $pattern = preg_replace('#\{([a-zA-Z0-9_]+)\}#', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    public function dispatch(): void
    {
        $requestMethod = Request::getMethod();
        $requestPath   = Request::getPath();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            if (preg_match($route['pattern'], $requestPath, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $handler = $route['handler'];

                if (is_callable($handler)) {
                    call_user_func_array($handler, $params);
                    return;
                }

                if (is_array($handler) && count($handler) === 2) {
                    [$class, $method] = $handler;
                    if (class_exists($class)) {
                        $controller = new $class();
                        if (method_exists($controller, $method)) {
                            call_user_func_array([$controller, $method], $params);
                            return;
                        }
                    }
                }
            }
        }

        if (str_starts_with($requestPath, '/api/')) {
            Response::json(['error' => 'API endpoint not found', 'path' => $requestPath], 404);
        } else {
            Response::setStatusCode(404);
            $controller = new Controller();
            echo $controller->render('customer/errors/404', ['path' => $requestPath], 'minimal');
        }
    }
}
