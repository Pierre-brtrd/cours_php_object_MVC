<?php

namespace App\Controllers\Backend;

use App\Core\Route;
use App\Form\UserForm;
use App\Core\Controller;
use App\Models\UserModel;

class UserController extends Controller
{
    public function __construct(
        private UserModel $userModel = new UserModel()
    ) {
    }

    /**
     * Affiche la page d'admin des utilisateurs
     *
     * @return void
     */
    #[Route('admin.user.index', '/admin/users', ['GET'])]
    public function user(): void
    {
        $this->isAdmin();
        // On appelle la vue avec la fonction render en lui passant les données
        $this->render('admin/users', 'admin', [
            'meta' => [
                'title' => 'Admin des users'
            ],
            'users' => $this->userModel->findAll(),
            'token' => $_SESSION['token'] = bin2hex(random_bytes(35)),
        ]);
    }

    #[Route('admin.user.edit', '/admin/user/edit/([0-9]+)', ['GET', 'POST'])]
    public function edit(int $id): void
    {
        // On vérifie si l'utilisateur est connecté
        $this->isAdmin();

        // On vérifie que l'utilisateur existe dans la BDD

        // On cherche l'utilisateur avec l'id
        $user = $this->userModel->find($id);

        // Si l'utilisateur n'existe pas, on redirige sur la liste des annonces
        if (!$user) {
            http_response_code(404);
            $_SESSION['error'] = "L'utilisateur recherché n'existe pas";
            header('Location: /admin/users');
            exit;
        }

        // On vérifie que le poste appartient à l'utilisateur connecté OU user Admin
        if ($user->id != $_SESSION['user']['id'] && !in_array("ROLE_ADMIN", $_SESSION['user']['roles'])) {
            $_SESSION['error'] = "Vous n'avez pas accès à cet utilisateur";
            header('Location: /admin/users');
            exit;
        }

        $form = new UserForm($this->userModel->hydrate($user));
        // On traire le formulaire
        if ($form->validate($_POST, ['email'])) {
            // Le formulaire est valide
            // On "nettoie" les champs
            $email = strip_tags($_POST['email']);
            $nom = strip_tags($_POST['nom']);
            $prenom = strip_tags($_POST['prenom']);
            $roles = $_POST['roles'];

            if ($email) {
                /** @var UserModel $user */
                $user
                    ->setNom($nom)
                    ->setPrenom($prenom)
                    ->setEmail($email)
                    ->setRoles("[\"$roles\"]")
                    ->update();

                $_SESSION['messages']['success'] = 'Utilisateur modifié avec succès';

                header('Location: /admin/users');
                exit();
            } else {
                $_SESSION['messages']['error'] = "Veuillez rentrer un email valide";
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === "POST") {
            $_SESSION['messages']['error'] = "Veuillez remplir tous les champs obligatoires";
        }

        // On envoie à la vue
        $this->render('users/modifier', 'base', [
            'form' => $form->create(),
            'meta' => [
                'title' => 'Modifier un utitilisateur'
            ]
        ]);
    }

    /**
     * Supprime un user
     *
     * @return void
     */
    #[Route('admin.user.delete', '/admin/deleteUser', ['POST'])]
    public function deleteUser(): void
    {
        $this->isAdmin();

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
