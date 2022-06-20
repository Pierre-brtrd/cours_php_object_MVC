<?php

namespace App\Controllers;

use App\Models\PosteModel;
use App\Models\UserModel;

class AdminController extends Controller
{
    public function index()
    {
        // On vérifie si l'utilisateur est admin
        if ($this->isAdmin()) {
            $this->render('admin/index', 'admin');
        }
    }

    public function postes()
    {
        if ($this->isAdmin()) {
            // On instancie le model correspondant à la table postes
            $posteModel = new PosteModel();

            // On va chercher toutes les annonces
            $postes = $posteModel->findAll();

            // On appelle la vue avec la fonction render en lui passant les données
            $this->render('admin/postes', 'admin', ['postes' => $postes]);
        }
    }

    /**
     * Supprime un poste
     *
     * @param integer $id
     * @return void
     */
    public function deletePoste(int $id)
    {
        if ($this->isAdmin()) {
            $poste = new PosteModel();
            $poste->delete($id);

            $_SESSION['message'] = "Poste supprimé avec succés";

            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
    }

    /**
     * Active ou désactive un poste
     *
     * @param integer $id
     * @return void
     */
    public function actifPoste(int $id)
    {
        if ($this->isAdmin()) {
            $posteModel = new PosteModel();
            $posteArray = $posteModel->find($id);

            if ($posteArray) {
                $poste = $posteModel->hydrate($posteArray);

                /**
                 *  if ($poste->getActif()) {
                 *      $poste->setActif(0)
                 *  } else {
                 *      $poste->setActif(1);
                 *  }
                 * 
                 *  Utilisation du ternaire pour simplifier le code sur 1 ligne :
                 */
                $poste->setActif($poste->getActif() ? 0 : 1);

                $poste->update();
            }

            echo $poste->getActif() ? 'border-success' : 'border-danger';
        }
    }

    /**
     * Affiche la page d'admin des utilisateurs
     *
     * @return void
     */
    public function user()
    {
        if ($this->isAdmin()) {
            // On instancie le model User
            $userModel = new UserModel();

            $users = $userModel->findAll();

            // On appelle la vue avec la fonction render en lui passant les données
            $this->render('admin/users', 'admin', ['users' => $users]);
        }
    }

    /**
     * Supprime un user
     *
     * @param integer $id
     * @return void
     */
    public function deleteUser(int $id)
    {
        if ($this->isAdmin()) {
            $user = new UserModel();
            $user->delete($id);

            $_SESSION['message'] = "Utilisateur supprimé avec succés";

            header('Location: ' . $_SERVER['HTTP_REFERER']);
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
        if (isset($_SESSION['user']) && in_array('ROLE_ADMIN', $_SESSION['user']['roles'])) {
            // On est admin
            return true;
        } else {
            // Pas admin, alors redirection vers page de connexion
            $_SESSION['error'] = "Vous n'avez pas accès à cette zone, connecté avec un compte Admin";
            header('Location: /user/login');
            exit;
        }
    }
}
