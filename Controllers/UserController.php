<?php

namespace App\Controllers;

use App\Core\Form;
use App\Models\UserModel;

class UserController extends Controller
{
    /**
     * Affiche la page de connexion utilisateur
     *
     * @return void
     */
    public function login()
    {
        if (Form::validate($_POST, ['email', 'password'])) {
            // Le formulaire est valide
            // On va chercher dans la base de données l'utilisateur l'email entrée
            $userModel = new UserModel();
            $userArray = $userModel->findOneByEmail(strip_tags($_POST['email']));

            // Si l'utilisateur n'existe pas
            if (!$userArray) {
                // On envoi un message de session erreur
                $_SESSION['error'] = "L'adresse email et/ou le mot de passe est incorrect";
                header('Location: /user/login');
                exit();
            }

            // L'utilisateur existe
            $userArray->roles = json_decode($userArray->roles);
            $user = $userModel->hydrate($userArray);

            /**
             * On vérifie si le password est correct
             * 
             * @var UserModel $user
             */
            if (
                password_verify($_POST['password'], $user->getPassword())
                && hash_equals($_POST['token'], $_SESSION['token'])
            ) {
                // Le mot de passe est bon
                // On crée la session
                $user->setSession();
                header('Location: /');
                exit();
            } else {
                $_SESSION['error'] = "L'adresse email et/ou le mot de passe est incorrect";
                header('Location: /user/login');
                exit();
            }
        }

        $form = new Form();
        $form->startForm('POST', '', ['class' => 'form card p-3'])
            ->startGroup(['class' => 'form-group'])
            ->addLabelFor('email', 'Email :', ['class' => 'form-label'])
            ->addInput('email', 'email', [
                'class' => 'form-control',
                'id' => 'email',
                'required' => true
            ])
            ->endGroup()
            ->startGroup(['class' => 'form-group mt-2'])
            ->addLabelFor('password', 'Mot de passe :', ['class' => 'form-label'])
            ->addInput('password', 'password', [
                'class' => 'form-control',
                'id' => 'password',
                'required' => true
            ])
            ->endGroup()
            ->addInput('hidden', 'token', [
                'value' => $_SESSION['token'] = bin2hex(random_bytes(35)),
            ])
            ->addButton('Me connecter', ['class' => 'btn btn-primary mt-4 mx-auto'])
            ->endForm();

        $this->render('users/login', 'base', ['loginForm' => $form->create()]);
    }

    /**
     * Inscription des utilisateurs
     *
     * @return void
     */
    public function register()
    {
        // Vérification si le formulaire est valide
        if (
            Form::validate($_POST, ['nom', 'prenom', 'email', 'password'])
            && hash_equals($_POST['token'], $_SESSION['token'])
        ) {
            // Le formulaire est valide
            // On "nettoie" les champs
            $email = strip_tags($_POST['email']);
            $nom = strip_tags($_POST['nom']);
            $prenom = strip_tags($_POST['prenom']);

            // Chiffrement du mot de passe
            $pass = password_hash($_POST['password'], PASSWORD_ARGON2I);

            // On hydrate l'utilisateur
            $user = new UserModel();
            $user->setEmail($email)
                ->setNom($nom)
                ->setPrenom($prenom)
                ->setPassword($pass);

            // On envoi l'utilisateur en BDD
            $user->create();
        } else {
            $_SESSION['error'] = !empty($_POST) ? "Le formulaire est incomplet" : '';
            $email = (isset($_POST['email'])) ? strip_tags($_POST['email']) : '';
            $nom = (isset($_POST['nom'])) ? strip_tags($_POST['nom']) : '';
            $prenom = (isset($_POST['prenom'])) ? strip_tags($_POST['prenom']) : '';
        }

        $form = new Form();

        $form->startForm('POST', '', ['class' => 'form card p-3'])
            ->startGroup(['class' => 'form-group'])
            ->addLabelFor('nom', 'Votre nom :', ['class' => 'form-label'])
            ->addInput('text', 'nom', [
                'class' => 'form-control',
                'id' => 'nom',
                'value' => $nom,
                'required' => true
            ])
            ->endGroup()
            ->startGroup(['class' => 'form-group mt-2'])
            ->addLabelFor('prenom', 'Votre prénom :', ['class' => 'form-label'])
            ->addInput('text', 'prenom', [
                'class' => 'form-control',
                'id' => 'prenom',
                'value' => $prenom,
                'required' => true
            ])
            ->endGroup()
            ->startGroup(['class' => 'form-group mt-2'])
            ->addLabelFor('email', 'Email :', ['class' => 'form-label'])
            ->addInput('email', 'email', [
                'class' => 'form-control',
                'id' => 'email',
                'vallue' => $email,
                'required' => true
            ])
            ->endGroup()
            ->startGroup(['class' => 'form-group mt-2'])
            ->addLabelFor('password', 'Mot de passe :', ['class' => 'form-label'])
            ->addInput('password', 'password', [
                'class' => 'form-control',
                'id' => 'password',
                'required' => true
            ])
            ->endGroup()
            ->addInput('hidden', 'token', [
                'value' => $_SESSION['token'] = bin2hex(random_bytes(35)),
            ])
            ->addButton('Inscription', ['class' => 'btn btn-primary mt-4 mx-auto'])
            ->endForm();

        $this->render('users/register', 'base', ['registerForm' => $form->create()]);
    }

    public function edit(int $id)
    {
        // On vérifie si l'utilisateur est connecté
        if (isset($_SESSION['user']) && !empty($_SESSION['user']['id'])) {
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
            if ($user->id != $_SESSION['user']['id'] && !in_array("ROLE_ADMIN", $_SESSION['user']['roles'])) {
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
                $userUpdate->update();

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
            if (in_array("ROLE_ADMIN", $_SESSION['user']['roles'])) {
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
                            'class' => 'form-control'
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
            $this->render('users/modifier', 'base', ['form' => $form->create()]);
        } else {
            // L'utilisateur n'est pas connecté
            $_SESSION['error'] = "Vous devez être connecté(e) pour accèder à cette page";
            header('Location: /user/login');
            exit;
        }
    }

    /**
     * Déconnecte l'utilisateur
     *
     * @return void
     */
    public function logout()
    {
        unset($_SESSION['user']);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}
