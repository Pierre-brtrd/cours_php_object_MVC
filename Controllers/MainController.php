<?php

namespace App\Controllers;

use App\Core\Form;
use App\Models\PosteModel;
use App\Models\UserModel;

class MainController extends Controller
{
    /**
     * Affiche la page d'accueil
     *
     * @return void
     */
    public function index()
    {
        $posteModel = new PosteModel();

        // On va chercher toutes les annonces
        $postes = $posteModel->findActiveWithLimit(3);

        $this->render('main/index', 'base', ['postes' => $postes]);
    }

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
                header('Location: /login');
                exit();
            }

            // L'utilisateur existe
            $userArray->roles = $userArray->roles  ? json_decode($userArray->roles) : null;
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
                header('Location: /login');
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
     * Déconnecte l'utilisateur
     *
     * @return void
     */
    public function logout()
    {
        unset($_SESSION['user']);

        $url = (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) ?
            $_SERVER['HTTP_REFERER'] : '/';

        header('Location: ' . $url);
        exit();
    }

    public function error(int $statusCode)
    {
        $this->render('error/error', 'base', ['code' => $statusCode]);
    }
}
