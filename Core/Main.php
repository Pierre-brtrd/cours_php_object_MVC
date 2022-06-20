<?php

namespace App\Core;

use App\Controllers\MainController;

/**
 * Routeur principal
 * 
 * @package App\Core
 */
class Main
{
    public function start()
    {
        // On démarre la session PHP
        session_start();

        /* Exemple d'URL de notre application :
            http://localhost:8005/controller/methode/parametres
            http://localhost:8005/postes/details/toto

            Création des réécriture d'url :
            http://localhost:8005/index.php?p=postes/details/toto

            Test de fonctionnement avec var_dump($_GET);
        */

        // ----------------------------------------------------------------
        // NETTOYAGE DE L'URL
        // On reitre le trailing slash (dernier slash de l'URL)
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

        // On gère les parametres de l'URL
        // p=controlleur/methode/parametres
        // On sépare les parametres dans un tableau
        $params = explode('/', $_GET['p']);

        if ($params[0] != '') {
            // On a au moins 1 parametre
            // On vérifie que le fichier du controlleur demandé existe
            $file = '/app/Controllers/' . ucfirst($params[0]) . 'Controller.php';

            if (file_exists($file)) {
                // On récupère le nom du controller a instancier
                // On doit mettre une maj en première lettre, on ajoute le namespace avant et on ajoute "Controller" après
                $controller = '\\App\\Controllers\\' . ucfirst(array_shift($params)) . 'Controller';

                // On instancie le controlleur
                $controller = new $controller;

                // On récupère le deuxième paramètre d'URL
                $action = (isset($params[0])) ? array_shift($params) : 'index';

                if (method_exists($controller, $action)) {
                    // S'il reste encore des parametres, on les passe à la methode
                    (isset($params[0])) ? call_user_func_array([$controller, $action], $params) : $controller->$action();
                } else {
                    http_response_code(404);
                    $controller = new MainController();

                    $controller->error(404);
                }
            } else {
                http_response_code(404);
                $controller = new MainController();

                $controller->error(404);
            }
        } else {
            // On a pas de parametres, donc on instancie le controller par défaut
            $controller = new MainController();

            // On appelle la methode index
            $controller->index();
        }
    }
}
