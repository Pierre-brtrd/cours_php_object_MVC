<?php

namespace App\Controllers\Frontend;

use App\Core\Route;
use App\Form\LoginForm;
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
    public function index(): string
    {
        return $this->render('main/index', 'base', [
            'meta' => [
                'title' => 'Homepage',
                'og:title' => 'Homepage | My App PHP Object',
                'description' => 'Vous cherchez un emploi ? Vous êtes sur la bonne application, retrouvez toutes les offres d\'emplois disponible sur le site.',
                'og:description' => 'Vous cherchez un emploi ? Vous êtes sur la bonne application, retrouvez toutes les offres d\'emplois disponible sur le site.',
            ],
            'postes' => (new PosteModel())->findActiveWithLimit(3),
        ]);
    }

    /**
     * Affiche la page de connexion utilisateur
     *
     * @return void
     */
    #[Route('login', '/login', ['GET', 'POST'])]
    public function login(): string
    {
        $form = new LoginForm();

        if ($form->validate($_POST, ['email', 'password'])) {
            // Le formulaire est valide
            // On va chercher dans la base de données l'utilisateur l'email entrée

            $user = (new UserModel())->findOneByEmail(strip_tags($_POST['email']));

            // Si l'utilisateur n'existe pas
            if (!$user) {
                // On envoi un message de session erreur
                $this->addFlash('danger', "L'adresse email et/ou le mot de passe est incorrect");

                return $this->redirect('login');
            }

            /**
             * On vérifie si le password est correct
             * 
             * @var UserModel $user
             */
            if (password_verify($_POST['password'], $user->getPassword())) {
                // Le mot de passe est bon
                // On crée la session
                $user->setSession();

                return $this->redirect('homepage');
            } else {
                $this->addFlash('danger', "L'adresse email et/ou le mot de passe est incorrect");

                return $this->redirect('login');
            }
        }

        return $this->render('users/login', 'base', [
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
    public function logout(): void
    {
        unset($_SESSION['user']);

        $url = (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) ?
            $_SERVER['HTTP_REFERER'] : '/';

        header('Location: ' . $url);
        exit();
    }

    public function error(int $statusCode): string
    {
        return $this->render('error/error', 'base', [
            'code' => $statusCode,
            'meta' => [
                'title' => "Erreur $statusCode",
            ]
        ]);
    }
}
