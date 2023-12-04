<?php

namespace App\Controllers\Frontend;

use App\Core\Route;
use App\Core\Controller;
use App\Models\UserModel;
use App\Form\RegisterForm;

class UserController extends Controller
{
    /**
     * Inscription des utilisateurs
     *
     * @return void
     */
    #[Route('user.register', '/register', ['GET', 'POST'])]
    public function register(): string
    {
        $form = new RegisterForm();

        // Vérification si le formulaire est valide
        if ($form->validate($_POST, ['nom', 'prenom', 'email', 'password'])) {
            // Le formulaire est valide
            // On "nettoie" les champs
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $nom = strip_tags($_POST['nom']);
            $prenom = strip_tags($_POST['prenom']);
            $pass = password_hash($_POST['password'], PASSWORD_ARGON2I);

            if ($email) {
                if (!(new UserModel())->findOneByEmail($email)) {
                    // On hydrate l'utilisateur
                    (new UserModel())->setEmail($email)
                        ->setNom($nom)
                        ->setPrenom($prenom)
                        ->setPassword($pass)
                        ->create();

                    $this->addFlash('success', "Vous êtes bien inscrit à notre application");

                    return $this->redirect('login');
                } else {
                    $this->addFlash('danger', "L'email existe déjà");
                }
            } else {
                $this->addFlash('danger', "L'email n'est pas valide");
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->addFlash('danger', "Le formulaire est incomplet");
            $email = (isset($_POST['email'])) ? strip_tags($_POST['email']) : '';
            $nom = (isset($_POST['nom'])) ? strip_tags($_POST['nom']) : '';
            $prenom = (isset($_POST['prenom'])) ? strip_tags($_POST['prenom']) : '';
        }

        return $this->render('users/register', 'base', ['registerForm' => $form->create()]);
    }
}
