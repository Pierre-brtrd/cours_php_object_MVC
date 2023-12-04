<?php

namespace App\Core;

abstract class Controller
{
    public function render(string $file, string $template = 'base', array $data = []): string
    {
        // On extrait le contenu de $data
        extract($data);

        // On démarre le buffer de sortie (Template Engine) Toute sortie est conservée en mémoire
        ob_start();
        // On charge la vue dans $contenu grâce au buffer de sortie
        require_once ROOT . '/Views/' . $file . '.php';

        // On transfer le buffer de sortie dans $contenu
        $contenu = ob_get_clean();

        // Template de page
        return require_once ROOT . '/Views/' . $template . '.php';
    }

    protected function isAdmin()
    {
        // On vérifie si on est connecté et si role Admin pour l'utilisateur
        if (isset($_SESSION['user']) && in_array('ROLE_ADMIN', $_SESSION['user']['roles'])) {
            // On est admin
            return true;
        } else {
            // Pas admin, alors redirection vers page de connexion
            http_response_code(403);
            $_SESSION['error'] = "Vous n'avez pas accès à cette zone, connecté avec un compte Admin";
            header('Location: /login');
            exit;
        }
    }

    protected function addFlash(string $type, string $message): void
    {
        $_SESSION['message'][$type] = $message;
    }

    protected function redirect(string $route): string
    {
        $router = new Router();

        header('Location: ' . $router->getUrl($route));
        exit();
    }
}
