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
            $user = $userModel->hydrate($userArray);

            // On vérifie si le password est correct
            if (password_verify($_POST['password'], $user->getPassword())) {
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
        if (Form::validate($_POST, ['nom', 'prenom', 'email', 'password'])) {
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
            ->addInput('nom', 'nom', [
                'class' => 'form-control',
                'id' => 'password',
                'value' => $nom,
                'required' => true
            ])
            ->endGroup()
            ->startGroup(['class' => 'form-group mt-2'])
            ->addLabelFor('prenom', 'Votre prénom :', ['class' => 'form-label'])
            ->addInput('prenom', 'prenom', [
                'class' => 'form-control',
                'id' => 'password',
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
            ->addButton('Inscription', ['class' => 'btn btn-primary mt-4 mx-auto'])
            ->endForm();

        $this->render('users/register', 'base', ['registerForm' => $form->create()]);
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
