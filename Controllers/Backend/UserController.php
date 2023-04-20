<?php

namespace App\Controllers\Backend;

use App\Core\Form;
use App\Core\Route;
use App\Core\Controller;
use App\Models\UserModel;

class UserController extends Controller
{
    /**
     * Affiche la page d'admin des utilisateurs
     *
     * @return void
     */
    #[Route('admin.user.index', '/admin/users', ['GET'])]
    public function user()
    {
        if ($this->isAdmin()) {
            // On instancie le model User
            $userModel = new UserModel();

            $users = $userModel->findAll();

            // On appelle la vue avec la fonction render en lui passant les données
            $this->render('admin/users', 'admin', [
                'meta' => [
                    'title' => 'Admin des users'
                ],
                'users' => $users,
                'token' => $_SESSION['token'] = bin2hex(random_bytes(35)),
            ]);
        }
    }

    #[Route('admin.user.edit', '/admin/user/edit/([0-9]+)', ['GET', 'POST'])]
    public function edit(int $id)
    {
        // On vérifie si l'utilisateur est connecté
        if ($this->isAdmin()) {
            // On vérifie que l'utilisateur existe dans la BDD
            // On instancie le model
            $userModel = new UserModel();

            // On cherche l'utilisateur avec l'id
            $user = $userModel->find($id);

            // Si l'utilisateur n'existe pas, on redirige sur la liste des annonces
            if (!$user) {
                http_response_code(404);
                $_SESSION['error'] = "L'utilisateur recherché n'existe pas";
                header('Location: /admin/user');
                exit;
            }

            // On vérifie que le poste appartient à l'utilisateur connecté OU user Admin
            if ($user->id != $_SESSION['user']['id'] && !in_array("ROLE_ADMIN", json_decode($_SESSION['user']['roles']))) {
                $_SESSION['error'] = "Vous n'avez pas accès à cet utilisateur";
                header('Location: /admin/user');
                exit;
            }

            // On traire le formulaire
            if (
                Form::validate($_POST, ['email'])
                && hash_equals($_POST['token'], $_SESSION['token'])
            ) {
                // Le formulaire est valide
                // On "nettoie" les champs
                $email = strip_tags($_POST['email']);
                $nom = strip_tags($_POST['nom']);
                $prenom = strip_tags($_POST['prenom']);

                // Chiffrement du mot de passe si modifier
                if (isset($_POST['password']) && !empty($_POST['password'])) {
                    $pass = password_hash($_POST['password'], PASSWORD_ARGON2I);
                }

                // Récupération des roles si existe
                if (isset($_POST['roles']) && !empty($_POST['roles'])) {
                    $roles[] = $_POST['roles'];
                }

                // On hydrate l'utilisateur
                $userUpdate = new UserModel();

                $userUpdate->setId($user->id)
                    ->setEmail($email)
                    ->setNom($nom)
                    ->setPrenom($prenom);

                isset($pass) ? $userUpdate->setPassword($pass) : null;
                isset($roles) ? $userUpdate->setRoles($roles) : null;

                // On envoi l'utilisateur en BDD
                $userUpdate->update($id);

                // On redirige
                $_SESSION['message'] = "Utilisateur modifié avec succès";

                header('Location: /admin/user');
                exit;
            }

            // On crée le formulaire
            $form = new Form();

            $form->startForm('POST', '', ['class' => 'form card p-3'])
                ->startGroup(['class' => 'form-group'])
                ->addLabelFor('nom', 'Votre nom :', ['class' => 'form-label'])
                ->addInput('text', 'nom', [
                    'class' => 'form-control',
                    'id' => 'nom',
                    'value' => $user->nom,
                    'required' => true
                ])
                ->endGroup()
                ->startGroup(['class' => 'form-group mt-2'])
                ->addLabelFor('prenom', 'Votre prénom :', ['class' => 'form-label'])
                ->addInput('text', 'prenom', [
                    'class' => 'form-control',
                    'id' => 'prenom',
                    'value' => $user->prenom,
                    'required' => true
                ])
                ->endGroup()
                ->startGroup(['class' => 'form-group mt-2'])
                ->addLabelFor('email', 'Email :', ['class' => 'form-label'])
                ->addInput('email', 'email', [
                    'class' => 'form-control',
                    'id' => 'email',
                    'value' => $user->email,
                    'required' => true
                ])
                ->endGroup()
                ->startGroup(['class' => 'form-group mt-2'])
                ->addLabelFor('password', 'Mot de passe :', ['class' => 'form-label'])
                ->addInput('password', 'password', [
                    'class' => 'form-control',
                    'id' => 'password'
                ])
                ->endGroup();

            // Si Admin, on peut modifier le role
            if (in_array("ROLE_ADMIN", json_decode($_SESSION['user']['roles']))) {
                $form->startGroup(['class' => 'form-group mt-2'])
                    ->addLabelFor('roles', 'Rôle :', ['class' => 'form-label'])
                    ->addSelectInput(
                        'roles',
                        [
                            'Utilisateur' => [
                                'value' => 'ROLE_USER',
                                'selected' => in_array('ROLE_USER', json_decode($user->roles)) ? true : null,
                            ],
                            'Éditeur' => [
                                'value' => 'ROLE_EDITOR',
                                'selected' => in_array('ROLE_EDITOR', json_decode($user->roles)) ? true : null,
                            ],
                            'Administrateur' => [
                                'value' => 'ROLE_ADMIN',
                                'selected' => in_array('ROLE_ADMIN', json_decode($user->roles)) ? true : null,
                            ],
                        ],
                        [
                            'class' => 'form-select'
                        ]
                    )
                    ->endGroup();
            }

            $form
                ->addInput('hidden', 'token', [
                    'value' => $_SESSION['token'] = bin2hex(random_bytes(35)),
                ])
                ->addButton('Modifier', ['class' => 'btn btn-primary mt-4 mx-auto'])
                ->endForm();

            // On envoie à la vue
            $this->render('users/modifier', 'base', [
                'form' => $form->create(),
                'meta' => [
                    'title' => 'Modifier un utitilisateur'
                ]
            ]);
        } else {
            // L'utilisateur n'est pas connecté
            $_SESSION['error'] = "Vous devez être connecté(e) pour accèder à cette page";
            header('Location: /login');
            exit;
        }
    }

    /**
     * Supprime un user
     *
     * @return void
     */
    #[Route('admin.user.delete', '/admin/deleteUser', ['POST'])]
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
