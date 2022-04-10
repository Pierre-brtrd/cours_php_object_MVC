<?php

namespace App\Controllers;

use App\Core\Form;
use App\Models\PosteModel;

class PostesController extends Controller
{
    /**
     * Affiche la liste des postes
     *
     * @return void
     */
    public function index()
    {
        // On instancie le model correspondant à la table postes
        $posteModel = new PosteModel();

        // On va chercher toutes les annonces
        $postes = $posteModel->findBy(['actif' => 1]);

        // On appelle la vue avec la fonction render en lui passant les données
        $this->render('postes/index', 'base', ['postes' => $postes]);
    }

    /**
     * Affiche un article
     *
     * @param integer $id
     * @return void
     */
    public function details(int $id)
    {
        // On instancie le model
        $posteModel = new PosteModel();

        // On recherche une annonce
        $poste = $posteModel->find($id);

        $this->render('postes/show', 'base', ['poste' => $poste]);
    }

    /**
     * Ajouter un poste
     *
     * @return void
     */
    public function ajouter()
    {
        // On vérifie si l'utilisateur est connecté
        if (isset($_SESSION['user']) && !empty($_SESSION['user']['id'])) {
            // L'utilisateur est connecté
            // On vérifie si le formulaire est complet
            if (Form::validate($_POST, ['titre', 'description'])) {
                // Le formulaire est complet
                // On se protège contre les failles XSS (injection de script en BDD via le form)
                $titre = strip_tags($_POST['titre']);
                $description = strip_tags($_POST['description']);

                // On instancie le model
                $poste = new PosteModel();

                // On hydrate
                $poste->setTitre($titre)
                    ->setDescription($description)
                    ->setUserId($_SESSION['user']['id']);

                // On enregistre
                $poste->create();

                // On redirige
                $_SESSION['message'] = "Poste enrgistré avec succès";

                header('Location: /postes');
                exit();
            } else {
                // Le formulaire est incomplet
                $_SESSION['error'] = !empty($_POST) ? "Le formulaire est incomplet" : '';
                $titre = (isset($_POST['titre'])) ? strip_tags($_POST['titre']) : '';
                $description = (isset($_POST['description'])) ? strip_tags($_POST['description']) : '';
            }

            $form = new Form();

            $form->startForm('POST', '', ['class' => 'form card p-3', 'enctype' => 'multipart/form-data'])
                ->startGroup(['class' => 'form-group'])
                ->addLabelFor('titre', 'Titre du poste :', ['class' => 'form-label'])
                ->addInput('text', 'titre', [
                    'id' => 'titre',
                    'class' => 'form-control',
                    'value' => $titre,
                    'required' => true
                ])
                ->endGroup()
                ->startGroup(['class' => 'form-group mt-3'])
                ->addLabelFor('description', 'Texte du poste :', ['class' => 'form-label'])
                ->addTextArea('description', $description, [
                    'id' => 'description',
                    'class' => 'form-control',
                    'required' => true
                ])
                ->endGroup()
                ->startGroup(['class' => 'form-group mt-3'])
                ->addLabelFor('image', 'Image du poste :', ['class' => 'form-label'])
                ->addInput('file', 'image', [
                    'id' => 'image',
                    'class' => 'form-control',
                ])
                ->endGroup()
                ->addButton('Ajouter', ['class' => 'btn btn-primary mt-4 mx-auto'])
                ->endForm();

            $this->render('postes/ajouter', 'base', ['form' => $form->create()]);
        } else {
            // L'utilisateur n'est pas connecté
            $_SESSION['error'] = "Vous devez être connecté(e) pour accèder à cette page";
            header('Location: /user/login');
            exit();
        }
    }

    /**
     * Modifier un poste
     *
     * @param integer $id
     * @return void
     */
    public function modifier(int $id)
    {
        // On vérifie si l'utilisateur est connecté
        if (isset($_SESSION['user']) && !empty($_SESSION['user']['id'])) {
            // On vérifie que le poste existe dans la BDD
            // On instancie le model
            $posteModel = new PosteModel();

            // On cherche le poste avec l'id
            $poste = $posteModel->find($id);

            // Si l'annonce n'existe pas, on redirige sur la liste des annonces
            if (!$poste) {
                http_response_code(404);
                $_SESSION['error'] = "Le poste recherché n'existe pas";
                header('Location: /postes');
                exit;
            }

            // On vérifie que le poste appartient à l'utilisateur connecté OU user Admin
            if ($poste->user_id != $_SESSION['user']['id'] && !in_array("ROLE_ADMIN", $_SESSION['user']['roles'])) {
                $_SESSION['error'] = "Vous n'avez pas accès à ce poste";
                header('Location: /postes');
                exit;
            }

            // On traire le formulaire
            if (Form::validate($_POST, ['titre', 'description'])) {
                // Le formulaire est complet
                // On se protège contre les failles XSS (injection de script en BDD via le form)
                $titre = strip_tags($_POST['titre']);
                $description = strip_tags($_POST['description']);

                // On instancie le model
                $posteUpdate = new PosteModel();

                // On hydrate
                $posteUpdate->setId($poste->id)
                    ->setTitre($titre)
                    ->setDescription($description);

                // On enregistre
                $posteUpdate->update();

                // On redirige
                $_SESSION['message'] = "Poste modifié avec succès";

                header('Location: /postes');
                exit();
            } else {
                $_SESSION['error'] = !empty($_POST) ? "Le formulaire est incomplet" : '';
                $titre = (isset($_POST['titre'])) ? strip_tags($_POST['titre']) : '';
                $description = (isset($_POST['description'])) ? strip_tags($_POST['description']) : '';
            }

            // On crée le formulaire
            $form = new Form();

            $form->startForm('POST', '', ['class' => 'form card p-3', 'enctype' => 'multipart/form-data'])
                ->startGroup(['class' => 'form-group'])
                ->addLabelFor('titre', 'Titre du poste :', ['class' => 'form-label'])
                ->addInput('text', 'titre', [
                    'id' => 'titre',
                    'class' => 'form-control',
                    'value' => $poste->titre,
                    'required' => true
                ])
                ->endGroup()
                ->startGroup(['class' => 'form-group mt-3'])
                ->addLabelFor('description', 'Texte du poste :', ['class' => 'form-label'])
                ->addTextArea('description', $poste->description, [
                    'id' => 'description',
                    'class' => 'form-control',
                    'required' => true
                ])
                ->endGroup()
                ->startGroup(['class' => 'form-group mt-3'])
                ->addLabelFor('image', 'Image du poste :', ['class' => 'form-label'])
                ->addInput('file', 'image', [
                    'id' => 'image',
                    'class' => 'form-control',
                ])
                ->endGroup()
                ->addButton('Modifier', ['class' => 'btn btn-primary mt-4 mx-auto'])
                ->endForm();

            // On envoie à la vue
            $this->render('postes/modifier', 'base', ['form' => $form->create()]);
        } else {
            // L'utilisateur n'est pas connecté
            $_SESSION['error'] = "Vous devez être connecté(e) pour accèder à cette page";
            header('Location: /user/login');
            exit;
        }
    }
}
