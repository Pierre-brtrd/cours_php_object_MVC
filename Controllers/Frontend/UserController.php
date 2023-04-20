<?php

namespace App\Controllers\Frontend;

use App\Core\Form;
use App\Core\Route;
use App\Core\Controller;
use App\Models\UserModel;

class UserController extends Controller
{
    /**
     * Inscription des utilisateurs
     *
     * @return void
     */
    #[Route('user.register', '/register', ['GET', 'POST'])]
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

            $_SESSION['message'] = "Vous êtes bien inscrit à notre application";

            header('Location: /login');
            exit();
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
}
