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
            $this->render('admin/postes', 'admin', [
                'postes' => $postes,
                'token' => $_SESSION['token'] = bin2hex(random_bytes(35)),
            ]);
        }
    }

    /**
     * Supprime un poste
     *
     *
     * @return void
     */
    public function deletePoste()
    {
        if ($this->isAdmin()) {
            $poste = new PosteModel();

            if (hash_equals($_POST['token'], $_SESSION['token']) && !empty($_POST['id'])) {
                $poste->delete($_POST['id']);
                $_SESSION['message'] = "Poste supprimé avec succés";
            } else {
                $_SESSION['error'] = "Une erreur est survenue";
            }

            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
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
                 * 
                 * @var PosteModel $poste
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
            $this->render('admin/users', 'admin', [
                'users' => $users,
                'token' => $_SESSION['token'] = bin2hex(random_bytes(35)),
            ]);
        }
    }

    /**
     * Supprime un user
     *
     * @return void
     */
    public function deleteUser()
    {
        if ($this->isAdmin()) {
            if (hash_equals($_POST['token'], $_SESSION['token']) && !empty($_POST['id'])) {
                $user = new UserModel();
                $user->delete($_POST['id']);
                $_SESSION['message'] = "Utilisateur supprimé avec succés";
            } else {
                $_SESSION['error'] = "Une erreur est survenue";
            }

            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
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
