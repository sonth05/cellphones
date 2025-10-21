<?php
namespace App;

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $path, string $handler): void
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    public function post(string $path, string $handler): void
    {
        $this->routes['POST'][$this->normalize($path)] = $handler;
    }

    public function dispatch(string $uri, string $method): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $path = $this->stripBase($path);
        // Treat direct calls to index.php as the root route
        if ($path === '/index.php') {
            $path = '/';
        }
        $handler = $this->routes[$method][$path] ?? null;
        if (!$handler) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }
        [$controller, $action] = explode('@', $handler);
        $controllerClass = 'App\\Controllers\\' . $controller;
        if (!class_exists($controllerClass)) {
            http_response_code(500);
            echo 'Controller not found';
            return;
        }
        $instance = new $controllerClass();
        if (!method_exists($instance, $action)) {
            http_response_code(500);
            echo 'Action not found';
            return;
        }
        $instance->$action();
    }

    private function normalize(string $path): string
    {
        if ($path === '') $path = '/';
        if ($path[0] !== '/') $path = '/' . $path;
        return rtrim($path, '/') ?: '/';
    }

    private function stripBase(string $path): string
    {
        $base = rtrim((string) (defined('BASE_URL') ? BASE_URL : ''), '/');
        if ($base && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }
        return $this->normalize($path);
    }
}
?>


