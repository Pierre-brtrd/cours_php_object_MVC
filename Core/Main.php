<?php

namespace App\Core;

class Main
{
    public function __construct(
        private Router $router = new Router
    ) {
    }

    public function start()
    {
        session_start();

        $uri = $_SERVER['REQUEST_URI'];

        // On verifie que l'URI n'est pas vide
        if (!empty($uri) && $uri != '/' && $uri[-1] === '/') {

            // On enleve le dernier /
            $uri = substr($uri, 0, -1);

            // On envoie un code de redirection permanente
            http_response_code(301);

            // On redirige vers l'URL sans le dernier /
            header('Location: ' . $uri);
            exit;
        }

        $this->initRouter();

        $this->router->handleRequest($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
    }

    private function initRouter(): void
    {
        $files = glob(\dirname(__DIR__) . '/Controllers/*.php');

        $files = array_merge_recursive(glob(\dirname(__DIR__) . '/Controllers/**/*.php', GLOB_BRACE), $files);

        foreach ($files as $file) {
            $classes[] = $this->convertFileToNamespace($file);
        }

        foreach ($classes as $class) {
            $methods = get_class_methods($class);
            foreach ($methods as $method) {
                $attributes = (new \ReflectionMethod($class, $method))->getAttributes(Route::class);
                foreach ($attributes as $attribute) {
                    if ($attribute) {
                        $route = $attribute->newInstance();
                        $route->setController($class);
                        $route->setAction($method);
                        $this->router->addRoute([
                            'name' => $route->getName(),
                            'url' => $route->getUrl(),
                            'methods' => $route->getMethods(),
                            'controller' => $route->getController(),
                            'action' => $route->getAction(),
                        ]);
                    }
                }
            }
        }
    }

    private function convertFileToNamespace(string $file): string
    {
        $file = substr($file, 1);
        $file = str_replace('/', '\\', $file);
        $file = substr($file, 0, -4);
        $file = ucfirst($file);

        return $file;
    }
}
