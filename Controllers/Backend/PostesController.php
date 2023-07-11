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
    public function postes(): void
    {
        $this->isAdmin();

        // On appelle la vue avec la fonction render en lui passant les données
        $this->render('admin/Postes/index', 'admin', [
            'meta' => [
                'title' => 'Admin postes'
            ],
            'postes' => $this->posteModel->findAll(),
            'token' => $_SESSION['token'] = bin2hex(random_bytes(35)),
        ]);
    }

    /**
     * Ajouter un poste
     *
     * @return void
     */
    #[Route('admin.poste.create', '/admin/poste/create', ['GET', 'POST'])]
    public function ajouter(): void
    {
        // On vérifie si l'utilisateur est connecté
        $this->isAdmin();
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

            // On instancie le model, on met à jour les données et on envoie en BDD
            $this->posteModel
                ->setTitre($titre)
                ->setDescription($description)
                ->setImage(isset($image) && !empty($image) ? $image : '')
                ->setUserId($_SESSION['user']['id'])
                ->create();

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
    }



    /**
     * Modifier un poste
     *
     * @param integer $id
     * @return void
     */
    #[Route('admin.poste.edit', '/admin/poste/edit/([0-9]+)', ['GET', 'POST'])]
    public function modifier(string|int $id): void
    {
        // On vérifie si l'utilisateur est connecté
        $this->isAdmin();
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
            ->addSelect('user', $userArr, [
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
    }

    /**
     * Supprime un poste
     *
     *
     * @return void
     */
    #[Route('admin.postes.delete', '/admin/deletePoste', ['POST'])]
    public function deletePoste(): void
    {
        $this->isAdmin();

        $poste = $this->posteModel->find(!empty($_POST['id']) ? $_POST['id'] : 0);

        if (hash_equals($_POST['token'], $_SESSION['token']) && $poste) {
            $this->posteModel
                ->hydrate($poste)
                ->delete();

            $_SESSION['message'] = "Poste supprimé avec succés";
        } else {
            $_SESSION['error'] = "Une erreur est survenue";
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    /**
     * Active ou désactive un poste
     *
     * @param integer $id
     * @return void
     */
    #[Route('admin.poste.visibility', '/admin/actifPoste/([0-9]+)',  ['GET'])]
    public function actifPoste(int $id): void
    {
        $this->isAdmin();

        $poste = $this->posteModel->find($id);

        if (!$poste) {
            http_response_code(404);
            echo json_encode([
                'data' => [
                    'status' => 'Error',
                    'message' => 'Article non trouvé, veuillez vérifier l\'id',
                ]
            ]);

            return;
        }

        $poste = $this->posteModel->hydrate($poste);

        /** @var PosteModel $poste */
        $poste
            ->setActif(!$poste->getActif())
            ->update();

        http_response_code(201);

        echo json_encode([
            'data' => [
                'status' => 'Success',
                'message' => 'Article modifié',
                'actif' => $poste->getActif(),
            ]
        ]);

        return;
    }
}
