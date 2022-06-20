<?php

namespace App\Controllers;

use App\Models\PosteModel;

class MainController extends Controller
{
    /**
     * Affiche la page d'accueil
     *
     * @return void
     */
    public function index()
    {
        $posteModel = new PosteModel();

        // On va chercher toutes les annonces
        $postes = $posteModel->findBy(['actif' => 1]);

        $this->render('main/index', 'base', ['postes' => $postes]);
    }

    public function error(int $statusCode)
    {
        $this->render('error/error', 'base', ['code' => $statusCode]);
    }
}
