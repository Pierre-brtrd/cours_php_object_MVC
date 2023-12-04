<?php

namespace App\Controllers\Frontend;

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

    /**
     * Affiche la page de liste poste actif
     *
     * @return void
     */
    #[Route('poste.index', '/postes(\?page=\d+)?', ['GET'])]
    public function index(?string $page = null): string
    {
        $page = preg_match('/\d+/', $page ?: '', $matches) ? (int) $matches[0] : 1;

        $postes = $this->posteModel->findAllWithPagination(6, $page, true);

        return $this->render('postes/Index/index', 'base', [
            'meta' => [
                'title' => 'Liste des postes',
                'og:title' => 'Liste des postes | My app PHP Object',
                'description' => 'Découvrez tous les postes disponible. Trouvez un emploi facilement grâce à toutes nos offres.',
                'og:description' => 'Découvrez tous les postes disponible. Trouvez un emploi facilement grâce à toutes nos offres.',
            ],
            'postes' => $postes['postes'],
            'page' => $page,
            'totalPage' => $postes['pages'],
        ]);
    }

    /**
     * Affiche un article
     *
     * @param integer $id
     * @return void
     */
    #[Route('poste.show', '/postes/details/([0-9]+)', ['GET'])]
    public function details(int $id): string
    {
        // On recherche une annonce
        $poste = $this->posteModel->findOneActiveWithAuthor($id);

        if (!$poste) {
            $this->addFlash('danger', "Poste non trouvé");

            return $this->redirect('poste.index');
        }

        return $this->render('postes/Show/show', 'base', [
            'meta' => [
                'title' => $poste->titre,
                'og:title' => "$poste->titre | My app PHP Object",
                'twitter:title' => "$poste->titre | My app PHP Object",
                'description' => strlen($poste->description) > 150 ? substr($poste->description, 0, 150) . '...' : $poste->description,
                'og:description' => strlen($poste->description) > 150 ? substr($poste->description, 0, 150) . '...' : $poste->description,
                'twitter:description' => strlen($poste->description) > 150 ? substr($poste->description, 0, 150) . '...' : $poste->description,
                'og:image' => $poste->image ? "https://$_SERVER[HTTP_HOST]/uploads/postes/$poste->image" : null,
                'twitter:image' => $poste->image ? "https://$_SERVER[HTTP_HOST]/uploads/postes/$poste->image" : null,
                'twitter:card' => 'summary',
            ],
            'poste' => $poste
        ]);
    }

    /**
     * Affiche les postes par auteur
     *
     * @param integer $id
     * @return void
     */
    #[Route('poste.show', '/postes/auteur/([0-9]+)', ['GET'])]
    public function auteur(int $id): string
    {
        $postes = $this->posteModel->findBy(['userId' => $id]);
        $auteur = $this->userModel->find($id);

        if (!$postes) {
            $this->addFlash('danger', "Poste non trouvé");

            return $this->redirect('poste.index');
        }

        return $this->render('Postes/auteur', 'base', [
            'meta' => [
                'title' => "Liste des poste de {$auteur->getPrenom()} {$auteur->getNom()}",
                'og:title' => "Liste des poste de {$auteur->getPrenom()} {$auteur->getNom()} | My app PHP Object",
                'description' => "Découvrez les postes de {$auteur->getPrenom()} {$auteur->getNom()}, trouvez un emploi grâce à {$auteur->getPrenom()} {$auteur->getNom()}.",
                'og:description' => "Découvrez les postes de {$auteur->getPrenom()} {$auteur->getNom()}, trouvez un emploi grâce à {$auteur->getPrenom()} {$auteur->getNom()}.",
            ],
            'postes' => $postes,
            'auteur' => $auteur
        ]);
    }
}
