<?php

namespace App\Controllers\Backend;

use App\Core\Route;
use App\Core\Controller;
use App\Models\UserModel;

class AdminController extends Controller
{
    #[Route('admin.index', '/admin', ['GET'])]
    public function index(): void
    {
        // On vérifie si l'utilisateur est admin
        $this->isAdmin();

        $this->render('admin/index', 'admin', [
            'meta' => [
                'title' => 'Administration du site',
                'description' => 'Gérez le site, retrouvez la liste des utilisateurs ainsi que la liste des postes pour les gérer',
            ],
        ]);
    }
}
