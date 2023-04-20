<?php

namespace App\Controllers\Backend;

use App\Core\Route;
use App\Core\Controller;
use App\Models\UserModel;

class AdminController extends Controller
{
    #[Route('admin.index', '/admin', ['GET'])]
    public function index()
    {
        // On vérifie si l'utilisateur est admin
        if ($this->isAdmin()) {
            $this->render('admin/index', 'admin', [
                'meta' => [
                    'title' => 'Administration du site',
                    'description' => 'Gérez le site, retrouvez la liste des utilisateurs ainsi que la liste des postes pour les gérer',
                ],
            ]);
        }
    }

    /**
     * Vérifie si on est Admin
     *
     * @return boolean
     */
    private function isAdmin()
    {
        // On vérifie si on est connecté et si role Admin pour l'utilisateur
        if (isset($_SESSION['user']) && in_array('ROLE_ADMIN', json_decode($_SESSION['user']['roles']))) {
            // On est admin
            return true;
        } else {
            // Pas admin, alors redirection vers page de connexion
            $_SESSION['error'] = "Vous n'avez pas accès à cette zone, connecté avec un compte Admin";
            header('Location: /login');
            exit;
        }
    }
}
