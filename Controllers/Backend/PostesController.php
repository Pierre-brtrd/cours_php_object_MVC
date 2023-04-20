<?php

namespace App\Controllers\Backend;

use DateTime;
use App\Core\Form;
use App\Core\Route;
use App\Core\Controller;
use App\Models\UserModel;
use App\Models\PosteModel;

class PostesController extends Controller
{
    public function __construct(
        private PosteModel $posteModel = new PosteModel,
        private UserModel $userModel = new UserModel
    ) {
    }

    #[Route('admin.poste.index', '/admin/postes', ['GET'])]
    public function postes()
    {
        if ($this->isAdmin()) {
            // On instancie le model correspondant à la table postes
            $posteModel = new PosteModel();

            // On va chercher toutes les annonces
            $postes = $posteModel->findAll();

            // On appelle la vue avec la fonction render en lui passant les données
            $this->render('admin/Postes/index', 'admin', [
                'meta' => [
                    'title' => 'Admin postes'
                ],
                'postes' => $postes,
                'token' => $_SESSION['token'] = bin2hex(random_bytes(35)),
            ]);
        }
    }

    /**
     * Ajouter un poste
     *
     * @return void
     */
    #[Route('admin.poste.creat', '/admin/poste/create', ['GET', 'POST'])]
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
                $poste = $this->posteModel
                    ->setTitre($titre)
                    ->setDescription($description)
                    ->setImage(isset($image) && !empty($image) ? $image : '')
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

            $this->render('postes/ajouter', 'base', [
                'meta' => [
                    'title' => 'Créer un poste',
                    'description' => 'Créez un poste et proposez une offre d\'emploi pour trouver de bon profil',
                ],
                'form' => $form->create()
            ]);
        } else {
            // L'utilisateur n'est pas connecté
            $_SESSION['error'] = "Vous devez être connecté(e) pour accèder à cette page";
            header('Location: /login');
            exit();
        }
    }

    /**
     * Modifier un poste
     *
     * @param integer $id
     * @return void
     */
    #[Route('admin.poste.edit', '/admin/poste/edit/([0-9]+)', ['GET', 'POST'])]
    public function modifier(string|int $id)
    {
        // On vérifie si l'utilisateur est connecté
        if (isset($_SESSION['user']) && !empty($_SESSION['user']['id'])) {
            // On vérifie que le poste existe dans la BDD
            // On instancie le model

            // On cherche le poste avec l'id
            $poste = is_numeric($id) ? $this->posteModel->find($id) : null;

            // Si l'annonce n'existe pas, on redirige sur la liste des annonces
            if (!$poste) {
                http_response_code(404);
                $_SESSION['error'] = "Le poste recherché n'existe pas";
                header('Location: /postes');
                exit;
            }

            // On vérifie que le poste appartient à l'utilisateur connecté OU user Admin
            if ($poste->userId != $_SESSION['user']['id'] && !in_array("ROLE_ADMIN", json_decode($_SESSION['user']['roles']))) {
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
                $posteUpdate = $this->posteModel
                    ->setId($poste->id)
                    ->setTitre($titre)
                    ->setDescription($description);

                isset($image) ? $posteUpdate->setImage($image) : null;
                isset($_POST['user']) ? $posteUpdate->setUserId($_POST['user']) : null;

                // On enregistre
                $posteUpdate->update($id);

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

            $userArr = [];

            foreach ($this->userModel->findAll() as $user) {

                if ($user->id == $poste->userId) {
                    $userArr["$user->prenom $user->nom"] = [
                        'value' => $user->id,
                        'selected' => true,
                    ];
                } else {
                    $userArr["$user->prenom $user->nom"] = [
                        'value' => $user->id,
                    ];
                }
            }



            $form->startForm('POST', '', ['class' => 'form card p-3', 'enctype' => 'multipart/form-data'])
                ->startGroup(['class' => 'row'])
                ->startGroup(['class' => 'col-md-6'])
                ->addLabelFor('titre', 'Titre du poste :', ['class' => 'form-label'])
                ->addInput('text', 'titre', [
                    'id' => 'titre',
                    'class' => 'form-control',
                    'value' => $poste->titre,
                    'required' => true
                ])
                ->endGroup()
                ->startGroup(['class' => 'col-md-6'])
                ->addLabelFor('user', 'Auteur:', ['class' => 'form-label'])
                ->addSelectInput('user', $userArr, [
                    'class' => 'form-select',
                    'id' => 'user'
                ])
                ->endGroup()
                ->endGroup()
                ->startGroup(['class' => 'form-group mt-3'])
                ->addLabelFor('description', 'Texte du poste :', ['class' => 'form-label'])
                ->addTextArea('description', $poste->description, [
                    'id' => 'description',
                    'class' => 'form-control',
                    'required' => true,
                    'rows' => 6,
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
            $this->render('postes/modifier', 'base', [
                'meta' => [
                    'title' => "Modifier le poste $poste->titre",
                    'description' => 'Modifiez un poste et proposez une offre d\'emploi pour trouver de bon profil',
                ],
                'form' => $form->create()
            ]);
        } else {
            // L'utilisateur n'est pas connecté
            $_SESSION['error'] = "Vous devez être connecté(e) pour accèder à cette page";
            header('Location: /login');
            exit;
        }
    }

    /**
     * Supprime un poste
     *
     *
     * @return void
     */
    #[Route('admin.postes.delete', '/admin/deletePoste', ['POST'])]
    public function deletePoste()
    {
        if ($this->isAdmin()) {
            $poste = new PosteModel();

            if (hash_equals($_POST['token'], $_SESSION['token']) && !empty($_POST['id'])) {
                $poste->delete($_POST['id']);
                $_SESSION['message'] = "Poste supprimé avec succés";
            } else {
                $_SESSION['error'] = "Une erreur est survenue";
            }

            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }

    /**
     * Active ou désactive un poste
     *
     * @param integer $id
     * @return void
     */
    #[Route('admin.poste.visibility', '/admin/actifPoste/([0-9]+)',  ['GET'])]
    public function actifPoste(int $id)
    {
        if ($this->isAdmin()) {
            $posteModel = new PosteModel();
            $posteArray = $posteModel->find($id);

            if ($posteArray) {
                $poste = $posteModel->hydrate($posteArray);

                /**
                 *  if ($poste->getActif()) {
                 *      $poste->setActif(0)
                 *  } else {
                 *      $poste->setActif(1);
                 *  }
                 * 
                 *  Utilisation du ternaire pour simplifier le code sur 1 ligne :
                 * 
                 * @var PosteModel $poste
                 */
                $poste->setActif($poste->getActif() ? 0 : 1);

                $poste->update($id);
            }

            echo $poste->getActif() ? 'border-success' : 'border-danger';
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
