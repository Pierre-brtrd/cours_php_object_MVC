<?php

namespace App\Core;

abstract class Controller
{
    public function render(string $file, string $template = 'base', array $data = []): Response
    {
        // Extraction des données pour rendre les clés accessibles comme variables dans la vue
        extract($data);

        // On démarre le buffer et on inclut la vue
        ob_start();
        include ROOT . '/Views/' . $file . '.php';
        // Le contenu de la vue est capturé dans la variable $contenu
        $contenu = ob_get_clean();

        // On démarre un nouveau buffer pour le template
        ob_start();
        include ROOT . '/Views/' . $template . '.php';
        // Le contenu final du template (qui utilise $contenu) est capturé
        $finalContent = ob_get_clean();

        return new Response($finalContent);
    }

    protected function isAdmin(): Response|bool
    {
        // On vérifie si on est connecté et si role Admin pour l'utilisateur
        if (isset($_SESSION['user']) && in_array('ROLE_ADMIN', $_SESSION['user']['roles'])) {
            // On est admin
            return true;
        } else {
            // Pas admin, alors redirection vers page de connexion
            $_SESSION['error'] = "Vous n'avez pas accès à cette zone, connecté avec un compte Admin";

            return $this->redirect('login', 403);
        }
    }

    protected function addFlash(string $type, string $message): void
    {
        $_SESSION['message'][$type] = $message;
    }

    protected function redirect(string $route, int $status = 302, array $params = [], bool $url = false): Response
    {
        $path = $url ? $route : Router::getUrl($route, $params);

        return new Response('', $status, ['Location' => $path]);
    }
}
