<?php

namespace App;

class Autoloader
{
    static function register()
    {
        spl_autoload_register(
            [
                __CLASS__,
                'autoload'
            ]
        );
    }

    static function autoload($class)
    {
        // Récupération du namespace de la classe instanciée
        // Retirer le App\ pour utiliser __NAMESPACE__
        $class = str_replace(__NAMESPACE__ . '\\', '', $class);

        // On remplace les \ par des / pour le chemain d'accès
        $class = str_replace('\\', "/", $class);

        // On vérifie si le fichier existe
        $fichier = __DIR__ . '/' . $class . '.php';

        if (file_exists($fichier)) {
            // On charge le bon fichier
            include_once $fichier;
        }
    }
}
