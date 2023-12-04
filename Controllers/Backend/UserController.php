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
    public function user(): string
    {
        $this->isAdmin();
        // On appelle la vue avec la fonction render en lui passant les données
        return $this->render('admin/users', 'admin', [
            'meta' => [
                'title' => 'Admin des users'
            ],
            'users' => $this->userModel->findAll(),
            'token' => $_SESSION['token'] = bin2hex(random_bytes(35)),
        ]);
    }

    #[Route('admin.user.edit', '/admin/user/edit/([0-9]+)', ['GET', 'POST'])]
    public function edit(int $id): string
    {
        // On vérifie si l'utilisateur est connecté
        $this->isAdmin();

        // On vérifie que l'utilisateur existe dans la BDD

        // On cherche l'utilisateur avec l'id
        $user = $this->userModel->find($id);

        // Si l'utilisateur n'existe pas, on redirige sur la liste des annonces
        if (!$user) {
            http_response_code(404);
            $this->addFlash('danger', "L'utilisateur recherché n'existe pas");

            return $this->redirect('admin.user.index');
        }

        // On vérifie que le poste appartient à l'utilisateur connecté OU user Admin
        if ($user->getId() != $_SESSION['user']['id'] && !in_array("ROLE_ADMIN", $_SESSION['user']['roles'])) {
            $this->addFlash('danger', "Vous n'avez pas accès à cet utilisateur");

            return $this->redirect('admin.user.index');
        }

        $form = new UserForm($user);
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

                $this->addFlash('success', "Utilisateur modifié avec succès");

                return $this->redirect('admin.user.index');
            } else {
                $this->addFlash('danger', "Veuillez rentrer un email valide");
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === "POST") {
            $this->addFlash('danger', "Veuillez remplir tous les champs obligatoires");
        }

        // On envoie à la vue
        return $this->render('users/modifier', 'base', [
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
            $user = $this->userModel->find($_POST['id']);

            if ($user) {
                if ($user->isAuthor()) {
                    $this->addFlash('danger', "Vous ne pouvez pas supprimer un utilisateur qui a des postes");
                } else {
                    $user->delete();
                    $this->addFlash('success', "Utilisateur supprimé avec succés");
                }
            } else {
                $this->addFlash('danger', "L'utilisateur n'existe pas");
            }
        } else {
            $this->addFlash('danger', "Une erreur est survenue");
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}
