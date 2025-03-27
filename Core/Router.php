<?php

namespace App\Core;

use App\Controllers\Frontend\MainController;
use App\Core\Response;

class Router
{
    private const ADMIN_PATH = '/admin';
    private const REDIRECT_LOGIN_PATH = '/login';

    /**
     * Stocke dans un tableau toutes les routes de l'application
     *
     * @var array<int, Route> $routes
     */
    private array $routes = [];

    public function addRoute(array $route): self
    {
        $this->routes[] = $route;

        return $this;
    }

    public function setRoutes(array $routes): self
    {
        $this->routes = $routes;

        return $this;
    }

    public function handleRequest($url, $method): void
    {
        if (preg_match("~^" . self::ADMIN_PATH . "~", $url)) {
            if (empty($_SESSION['user']) || !in_array('ROLE_ADMIN', $_SESSION['user']['roles'])) {
                $_SESSION['message']['error'] = "Vous n'avez pas accès à cette zone, connecté avec un compte Admin";

                (new Response('', 403, ['Location' => self::REDIRECT_LOGIN_PATH]))->send();
                return;
            }
        }

        foreach ($this->routes as $route) {
            if (preg_match("#^" . $route['url'] . "$#", $url, $matches) && in_array($method, $route['methods'])) {
                $controllerName = $route['controller'];
                $actionName = $route['action'];

                $controller = new $controllerName();
                $matches = array_slice($matches, 1);

                $params = [];

                foreach ($matches as $key => $value) {
                    if (!is_numeric($key)) {
                        $params[$key] = (int) $value;
                    }
                }

                $response = $controller->$actionName(...$params);

                if (!$response instanceof Response) {
                    $response = new Response($response);
                }
                $response->send();

                return;
            }
        }

        $controller = new MainController();

        $response = $controller->error(404);

        if (!$response instanceof Response) {
            $response = new Response($response, 404);
        }

        $response->send();
    }

    public static function getUrl(string $route, array $params = []): string
    {
        $cache = new Cache();
        $routeData = array_filter($cache->get('routes'), fn($value) => $value['name'] === $route);
        $routeData = array_shift($routeData);

        $url = $routeData['url'];

        if (strpos($url, '(?:') !== false) {
            if (isset($params['page'])) {
                // Remplacer le pattern par le paramètre fourni.
                // On suppose ici que le pattern exact est: (?:\?page=(?P<page>\d+))?
                $url = preg_replace(
                    '#\(\?:\?page=\(\?P<page>\\\d\+\)\)\?#',
                    '?page=' . $params['page'],
                    $url
                );
            } else {
                $optional = '(?:\?page=(?P<page>\d+))?';

                // On génère une expression régulière qui correspond exactement à cette portion,
                // en échappant correctement tous les caractères spéciaux.
                $pattern = '#' . preg_quote($optional, '#') . '#';
                $url = preg_replace($pattern, '', $url);
            }
        }

        return $url;
    }
}
