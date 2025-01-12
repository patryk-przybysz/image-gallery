<?php

declare(strict_types=1);

namespace App;

use App\Exceptions\RouteNotFoundException;

class Router
{
    private $routes = [
        "GET" => [],
        "POST" => [],
    ];


    public function register(string $method, string $route, callable $action): self
    {
        $this->routes[$method][$route] = $action;

        return $this;
    }


    public function get(string $route, callable $action)
    {
        return $this->register('GET', $route, $action);
    }


    public function post(string $route, callable $action)
    {
        return $this->register('POST', $route, $action);
    }


    public function normalizePath(string $path): string
    {
        $path = explode('?', $path)[0];
        $path = urldecode($path);
        return rtrim($path, '/') ?: '/';
    }


    public function resolve(string $requestUri, string $requestMethod)
    {
        $route = $this->normalizePath($requestUri);
        $action = $this->routes[$requestMethod][$route] ?? null;

        if (!$action) {
            throw new RouteNotFoundException();
        }

        if (is_callable($action)) {
            return call_user_func_array($action, []);
        }
    }
}
