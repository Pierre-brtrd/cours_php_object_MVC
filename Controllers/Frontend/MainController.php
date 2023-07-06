<?php

namespace App\Controllers\Frontend;

use App\Core\Form;
use App\Core\Route;
use App\Core\Controller;
use App\Models\UserModel;
use App\Models\PosteModel;

class MainController extends Controller
{
    /**
     * Affiche la page d'accueil
     *
     * @return void
     */
    #[Route('homepage', '/', ['GET'])]
    public function index()
    {
        $posteModel = new PosteModel();

        // On va chercher toutes les annonces
        $postes = $posteModel->findActiveWithLimit(3);

        var_dump($postes);

        $this->render('main/index', 'base', [
            'meta' => [
                'title' => 'Homepage',
                'og:title' => 'Homepage | My App PHP Object',
                'description' => 'Vous cherchez un emploi ? Vous êtes sur la bonne application, retrouvez toutes les offres d\'emplois disponible sur le site.',
                'og:description' => 'Vous cherchez un emploi ? Vous êtes sur la bonne application, retrouvez toutes les offres d\'emplois disponible sur le site.',
            ],
            'postes' => $postes
        ]);
    }

    /**
     * Affiche la page de connexion utilisateur
     *
     * @return void
     */
    #[Route('login', '/login', ['GET', 'POST'])]
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

        $this->render('users/login', 'base', [
            'meta' => [
                'title' => 'Se connecter',
                'description' => 'Connectez vous à votre compte pour retrouver votre profil, gérer vos postes et vos informations',
            ],
            'loginForm' => $form->create()
        ]);
    }

    /**
     * Déconnecte l'utilisateur
     *
     * @return void
     */
    #[Route('logout', '/logout', ['GET'])]
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
        $this->render('error/error', 'base', [
            'code' => $statusCode,
            'meta' => [
                'title' => "Erreur $statusCode",
            ]
        ]);
    }
}
