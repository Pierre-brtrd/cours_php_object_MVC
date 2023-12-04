<?php

namespace App\Core;

use App\Controllers\Frontend\MainController;

class Router
{
    /**
     * Undocumented variable
     *
     * @var array<int, Route> $routes
     */
    private array $routes = [];

    public function addRoute(array $route): self
    {
        $this->routes[] = $route;

        return $this;
    }

    public function handleRequest($url, $method)
    {
        foreach ($this->routes as $route) {
            if (preg_match("#^" . $route['url'] . "$#", $url, $matches) && in_array($method, $route['methods'])) {
                $controllerName = $route['controller'];
                $actionName = $route['action'];

                $controller = new $controllerName();
                $params = array_slice($matches, 1); // On récupère les paramètres à partir du 2e élément du tableau $matches
                $controller->$actionName(...$params);

                return;
            }
        }

        http_response_code(404);
        $controller = new MainController();

        $controller->error(404);
    }

    public function getUrl(string $route): string
    {
        $route = array_filter($_SESSION['routes'], function ($value) use ($route) {
            return $value['name'] === $route;
        });
        $route = array_shift($route);

        return $route['url'];
    }
}
