<?php

namespace App\Controllers\Backend;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Route;

class AdminController extends Controller
{
    #[Route('admin.index', '/admin', ['GET'])]
    public function index(): Response
    {
        // On vérifie si l'utilisateur est admin
        $this->isAdmin();

        return $this->render('admin/index', 'admin', [
            'meta' => [
                'title' => 'Administration du site',
                'description' => 'Gérez le site, retrouvez la liste des utilisateurs ainsi que la liste des postes pour les gérer',
            ],
        ]);
    }
}
