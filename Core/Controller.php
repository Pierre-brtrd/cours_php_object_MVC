<?php

namespace App\Core;

abstract class Controller
{
    public function render(string $file, string $template = 'base', array $data = [])
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
        require_once ROOT . '/Views/' . $template . '.php';
    }
}
