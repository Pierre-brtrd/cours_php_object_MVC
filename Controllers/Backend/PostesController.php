<?php

namespace App\Controllers\Backend;

use App\Core\Route;
use App\Form\PosteForm;
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

    #[Route('admin.poste.index', '/admin/postes(\?page=\d+)?', ['GET'])]
    public function postes(?string $page = null): void
    {
        $this->isAdmin();

        $page = preg_match('/\d+/', $page ?: '', $matches) ? (int) $matches[0] : 1;

        $postes = $this->posteModel->findAllWithPagination(6, $page);
        // On appelle la vue avec la fonction render en lui passant les données
        $this->render('admin/Postes/index', 'admin', [
            'meta' => [
                'title' => 'Admin postes'
            ],
            'postes' => $postes['postes'],
            'token' => $_SESSION['token'] = bin2hex(random_bytes(35)),
            'page' => $page,
            'totalPage' =>  $postes['pages'],
            'admin' => true,
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

        // Instance du formulaire
        $form = new PosteForm();

        // Validation du form
        if ($form->validate($_POST, ['titre', 'description'])) {
            // Nettoyage des données
            $titre = strip_tags($_POST['titre']);
            $description = strip_tags($_POST['description']);
            $actif = isset($_POST['actif']) ? true : false;

            // On envoie en BDD
            $this->posteModel
                ->setTitre($titre)
                ->setDescription($description)
                ->setActif($actif)
                ->setUserId($_SESSION['user']['id'])
                ->setImage($_FILES['image'])
                ->create();

            $_SESSION['message'] = "Article créé avec succès";

            header('Location: /admin/postes');
            exit();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['error'] = 'Veuillez remplir tous les champs obligatoires';

            $titre = (isset($_POST['titre'])) ? strip_tags($_POST['titre']) : '';
            $description = (isset($_POST['description'])) ? strip_tags($_POST['description']) : '';
        }

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
        if ($poste->userId != $_SESSION['user']['id'] && !in_array("ROLE_ADMIN", $_SESSION['user']['roles'])) {
            $_SESSION['error'] = "Vous n'avez pas accès à ce poste";
            header('Location: /postes');
            exit;
        }

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

        $form = new PosteForm($this->posteModel->hydrate($poste));

        // On traire le formulaire
        if ($form->validate($_POST, ['titre', 'description'])) {
            // Le formulaire est complet
            // On se protège contre les failles XSS (injection de script en BDD via le form)
            $titre = strip_tags($_POST['titre']);
            $description = strip_tags($_POST['description']);

            // On instancie le model
            $posteUpdate = $this->posteModel
                ->setId($poste->id)
                ->setTitre($titre)
                ->setDescription($description)
                ->setImage($_FILES['image']);

            isset($_POST['user']) ? $posteUpdate->setUserId($_POST['user']) : null;

            // On enregistre
            $posteUpdate->update();

            // On redirige
            $_SESSION['message'] = "Poste modifié avec succès";

            header('Location: /admin/postes');
            exit();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['error'] = !empty($_POST) ? "Le formulaire est incomplet" : '';
            $titre = (isset($_POST['titre'])) ? strip_tags($_POST['titre']) : '';
            $description = (isset($_POST['description'])) ? strip_tags($_POST['description']) : '';
        }

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
