<?php

namespace App\Controllers\Backend;

use App\Core\Route;
use App\Core\Controller;

class AdminController extends Controller
{
    #[Route('admin.index', '/admin', ['GET'])]
    public function index(): string
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
