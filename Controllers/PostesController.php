<?php

namespace App\Controllers;

use App\Core\Form;
use App\Models\PosteModel;
use App\Models\UserModel;
use DateTime;

class PostesController extends Controller
{
    /**
     * Affiche la page de liste poste actif
     *
     * @return void
     */
    public function index()
    {
        // On insrtancie le Model Poste
        $posteModel = new PosteModel();

        // On va chercher les postes
        $postes = $posteModel->findActiveWithAuthor();

        $this->render('Postes/index', 'base', [
            'postes' => $postes
        ]);
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
     * Affiche les postes par auteur
     *
     * @param integer $id
     * @return void
     */
    public function auteur(int $id)
    {
        $posteModel = new PosteModel();
        $postes = $posteModel->findBy(['user_id' => $id]);

        $userModel = new UserModel();
        $auteur = $userModel->find($id);

        $this->render('Postes/auteur', 'base', [
            'postes' => $postes,
            'auteur' => $auteur
        ]);
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
            if (
                Form::validate($_POST, ['titre', 'description']) &&
                hash_equals($_POST['token'], $_SESSION['token'])
            ) {
                // Le formulaire est complet
                // On se protège contre les failles XSS (injection de script en BDD via le form)
                $titre = strip_tags($_POST['titre']);
                $description = strip_tags($_POST['description']);

                // On vérifie s'il y a une image et qu'il n'a pas d'erreur
                if ($_FILES['image'] && $_FILES['image']['error'] == 0) {
                    // On vérifie la taille de l'image
                    if ($_FILES['image']['size'] <= 1000000) {
                        // On vérifie l'extension de l'image
                        $infoFile = pathinfo($_FILES['image']['name']);
                        $extension = $infoFile['extension'];
                        $extensionAllowed = ['jpg', 'jpeg', 'png', 'gif'];

                        if (in_array($extension, $extensionAllowed)) {
                            // On remplace les espaces et on enregistre le fichier dans le dossier uploads
                            $file = str_replace(" ", "-", $_FILES['image']['name']);

                            move_uploaded_file(
                                str_replace(" ", ",", $_FILES['image']['tmp_name']),
                                '/app/public/uploads/postes/' . $file
                            );

                            $image = $file;
                        }
                    }
                }

                // On instancie le model
                $poste = new PosteModel();

                // On hydrate
                $poste->setTitre($titre)
                    ->setDescription($description)
                    ->setImage($image ? $image : '')
                    ->setUserId($_SESSION['user']['id']);

                // On enregistre
                $poste->create();

                // On redirige
                $_SESSION['message'] = "Poste enregistré avec succès";

                header('Location: /admin/postes');
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
                ->addInput('hidden', 'token', [
                    'value' => $_SESSION['token'] = bin2hex(random_bytes(35)),
                ])
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
            if (
                Form::validate($_POST, ['titre', 'description'])
                && hash_equals($_POST['token'], $_SESSION['token'])
            ) {
                // Le formulaire est complet
                // On se protège contre les failles XSS (injection de script en BDD via le form)
                $titre = strip_tags($_POST['titre']);
                $description = strip_tags($_POST['description']);

                // On vérifie s'il y a une image et qu'il n'a pas d'erreur
                if ($_FILES['image'] && $_FILES['image']['error'] == 0) {
                    // On vérifie la taille de l'image
                    if ($_FILES['image']['size'] <= 1000000) {
                        // On vérifie l'extension de l'image
                        $infoFile = pathinfo($_FILES['image']['name']);
                        $extension = $infoFile['extension'];
                        $extensionAllowed = ['jpg', 'jpeg', 'png', 'gif'];

                        if (in_array($extension, $extensionAllowed)) {
                            // On remplace les espaces et on enregistre le fichier dans le dossier uploads
                            $file = str_replace(' ', '-', $infoFile['filename'])
                                . (new DateTime())->format('Y-m-d_H:i:s') . '.' .
                                $infoFile['extension'];
                            if ($poste->image) {
                                $imagePath = "/app/public/uploads/postes/$poste->image";
                                if (file_exists($imagePath)) {
                                    unlink($imagePath);
                                }
                            }

                            move_uploaded_file(
                                str_replace(" ", ",", $_FILES['image']['tmp_name']),
                                '/app/public/uploads/postes/' . $file
                            );

                            $image = $file;
                        }
                    }
                }

                // On instancie le model
                $posteUpdate = new PosteModel();

                // On hydrate
                $posteUpdate->setId($poste->id)
                    ->setTitre($titre)
                    ->setDescription($description);

                isset($image) ? $posteUpdate->setImage($image) : '';

                // On enregistre
                $posteUpdate->update();

                // On redirige
                $_SESSION['message'] = "Poste modifié avec succès";

                header('Location: /admin/postes');
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
                ->endGroup();

            if ($poste->image) {
                $form->startGroup(['class' => 'form-group form-img mt-3'])
                    ->addImage("/uploads/postes/$poste->image")
                    ->endGroup();
            }

            $form->startGroup(['class' => 'form-group mt-3'])
                ->addLabelFor('image', 'Image du poste :', ['class' => 'form-label'])
                ->addInput('file', 'image', [
                    'id' => 'image',
                    'class' => 'form-control',
                ])
                ->endGroup()
                ->addInput('hidden', 'token', [
                    'value' => $_SESSION['token'] = bin2hex(random_bytes(35)),
                ])
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
