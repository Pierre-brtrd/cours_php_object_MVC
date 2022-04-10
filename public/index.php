<?php

use App\Autoloader;
use App\Core\Main;

// Définition de la contante contenant le dossier racine du projet
define('ROOT', dirname(__DIR__));

// On importe l'autoloader
require_once ROOT . '/Autoloader.php';
Autoloader::register();

// On instancie la class Main
$app = new Main();

// On démarre l'application
$app->start();
