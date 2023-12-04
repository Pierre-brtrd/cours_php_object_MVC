<?php

namespace App\Models;

use App\Core\Db;
use PDOStatement;

class Model extends Db
{
    /**
     * Va stocker le nom de la table
     *
     * @var string|null
     */
    protected ?string $table = null;

    /**
     * Va stocker la connexion en BDD
     *
     * @var Db|null
     */
    protected ?Db $database = null;

    /**
     * Trouve toutes les entrées d'une table
     *
     * @return array
     */
    public function findAll(): array
    {
        return $this->fetchHydrate(
            $this->runQuery("SELECT * FROM $this->table")->fetchAll()
        );
    }

    /**
     * Trouve une entrée en BDD de par son ID
     *
     * @param integer $id
     * @return object|boolean
     */
    public function find(int $id): object|bool
    {
        return $this->fetchHydrate(
            $this->runQuery("SELECT * FROM $this->table WHERE id = :id", ['id' => $id])->fetch()
        );
    }

    /**
     * Recherche des entrées en BDD avec un tableau de filtre
     *
     * @param array $criteres critère de filtre ex: ['id' => 1]
     * @return array
     */
    public function findBy(array $criteres): array
    {
        // SELECT * FROM articles WHERE actif = :actif AND id = :id

        // On prépare la récupération des champs et des valeurs de manière séparée
        $champs = [];
        $valeurs = [];

        // On parcourt le tableau de critères pour récupérer les champs et les valeurs
        // Exemple du tableau ['actif' => true, 'id' => 1]
        foreach ($criteres as $champ => $valeur) {
            // "actif = :actif"
            $champs[] = "$champ = :$champ";
            $valeurs[$champ] = $valeur;
        }

        // On transforme le tableau de champs en chaine de caractère pour l'intégrer
        // à la requêtes SQL
        $strChamps = implode(' AND ', $champs);

        // On execute la requetes SQL

        return $this->runQuery("SELECT * FROM $this->table WHERE $strChamps", $valeurs)->fetchAll();
    }

    public function paginate(string $sql, int $maxPerPage, int $page = 1, ?array $values = []): array
    {
        $offset = ($page - 1) * $maxPerPage;

        $values['limit'] = $maxPerPage;
        $values['offset'] = $offset;

        return $this->runQuery("$sql LIMIT :limit OFFSET :offset", $values)->fetchAll();
    }

    /**
     * Fonction de création d'une entrée en BDD
     *
     * @return \PDOStatement|null
     */
    public function create(): ?\PDOStatement
    {
        // Requête SQL à faire :
        // INSERT INTO articles (titre, description, created_at, actif) 
        // VALUES (:titre, :description, :created_at, :actif)

        // Initialiser les tableaux vide pour récupérer les données
        $champs = [];
        $valeurs = [];
        $marqueurs = [];

        // On boucle sur l'objet pour récupérer tous les champs et les valeurs
        foreach ($this as $champ => $valeur) {
            if ($valeur !== null && $champ !== 'table' && $champ !== 'database') {
                // actif
                $champs[] = $champ;

                // ['actif' => true]
                if (gettype($valeur) === 'boolean') {
                    $valeurs[$champ] = (int) $valeur;
                } elseif (gettype($valeur) === 'array') {
                    $valeurs[$champ] = json_encode($valeur);
                } elseif ($valeur instanceof \Datetime) {
                    $valeurs[$champ] = date_format($valeur, 'Y-m-d H:i:s');
                } else {
                    $valeurs[$champ] = $valeur;
                }

                // :actif
                $marqueurs[] = ":$champ";
            }
        }

        $strChamps = implode(', ', $champs);
        $strMarqueurs = implode(', ', $marqueurs);

        return $this->runQuery("INSERT INTO $this->table ($strChamps) VALUES ($strMarqueurs)", $valeurs);
    }

    /**
     * Méthode de mise à jour d'un objet en BDD
     *
     * @return \PDOStatement|null
     */
    public function update(): ?\PDOStatement
    {
        // Requête SQL à faire :
        // UPDATE articles SET titre = :titre, description = :description WHERE id = :id 

        // Initialiser les tableaux vide pour récupérer les données
        $champs = [];
        $valeurs = [];

        // On boucle sur l'objet pour récupérer tous les champs et les valeurs
        foreach ($this as $champ => $valeur) {
            if ($valeur !== null && $champ !== 'table' && $champ !== 'database' && $champ !== 'id') {
                // actif
                $champs[] = "$champ = :$champ";

                // ['actif' => true]
                if (gettype($valeur) === 'boolean') {
                    $valeurs[$champ] = (int) $valeur;
                } elseif (gettype($valeur) === 'array') {
                    $valeurs[$champ] = json_encode($valeur);
                } elseif ($valeur instanceof \Datetime) {
                    $valeurs[$champ] = date_format($valeur, 'Y-m-d H:i:s');
                } else {
                    $valeurs[$champ] = $valeur;
                }
            }
        }

        /** @var UserModel|PosteModel $this */
        $valeurs['id'] = $this->id;

        $strChamps = implode(', ', $champs);

        return $this->runQuery("UPDATE $this->table SET $strChamps WHERE id = :id", $valeurs);
    }

    /**
     * Méthode de suppression d'une entrée en BDD
     *
     * @return \PDOStatement|null
     */
    public function delete(): ?\PDOStatement
    {
        /** @var UserModel|PosteModel $this */
        if (isset($this->image)) {
            $this->removeImage(ROOT . "/public/images/$this->table/" . $this->image);
        }

        return $this->runQuery("DELETE FROM $this->table WHERE id = :id", ['id' => $this->id]);
    }

    public function uploadImage(array $image, bool $remove = false): string|bool
    {
        if (!empty($image['name'] && $image['error'] === 0)) {
            if ($image['size'] <= 1000000) {
                $fileInfo = pathinfo($image['name']);
                $extension = $fileInfo['extension'];
                $extensions_autorisees = ['jpg', 'jpeg', 'png', 'gif', 'svg'];

                if (in_array($fileInfo['extension'], $extensions_autorisees)) {
                    $nom = $fileInfo['filename'] . date_format(new \DateTime(), 'Y-m-d_H:i:s') . '.' . $extension;

                    if (!is_dir(ROOT . '/public/images/' . $this->table)) {
                        mkdir(ROOT . '/public/images/' . $this->table);
                    }

                    move_uploaded_file($image['tmp_name'], ROOT . '/public/images/' . $this->table . '/' . $nom);

                    if ($remove) {
                        /** @var PosteModel $this */
                        $this->removeImage(ROOT . '/public/images/' . $this->table . '/' . $this->image);
                    }

                    return $nom;
                }
            }
        }

        return false;
    }

    public function removeImage(string $path): bool
    {
        if (file_exists($path)) {
            unlink($path);

            return true;
        }

        return false;
    }

    /**
     * Méthode d'hydratation d'un objet à partir d'un tableau associatif
     *      $donnees = [
     *          'titre' => "Titre de l'objet",
     *          'description' => 'Desc',
     *          'actif' => true,
     *      ];
     * 
     *      RETOURNE:
     *          $article->setTitre('Titre de l'objet')
     *              ->setDescription('Desc')
     *              ->setActif(true);
     *
     * @param array|object $donnees
     * @return self
     */
    public function hydrate(array|object $donnees): self
    {
        // On parcourt le tableau de données
        foreach ($donnees as $key => $valeur) {
            // On récupère les setters
            $setter = 'set' . ucfirst($key);
            // $this->setTitre('Test')
            $this->$setter($valeur);
        }

        return $this;
    }

    /**
     * Fonction pour envoyer n'importe qu'elle requête SQL en BDD
     *
     * @param string $sql Requête SQL à envoyer
     * @param array|null $parametres Tableau associatif avec les marqeurs SQL (Facultatif)
     * @return \PDOStatement|null
     */
    public function runQuery(string $sql, ?array $parametres = null): ?\PDOStatement
    {
        // On récupère la connexion en BDD
        $this->database = Db::getInstance();

        // On vérifie s'il y a des paramètres à la requête SQL
        if ($parametres !== null) {
            // Requête préparée (avec marqueur dans la requête)
            $query = $this->database->prepare($sql);
            $query->execute($parametres);

            return $query;
        } else {
            // Requête simple (sans marqueur SQL)
            return $this->database->query($sql);
        }
    }

    protected function fetchHydrate(mixed $query): array|static|bool
    {
        if (is_array($query) && count($query) > 1) {
            $data = array_map(function (mixed $value) {
                return (new static)->hydrate($value);
            }, $query);

            return $data;
        } elseif (!empty($query)) {
            return (new static)->hydrate($query);
        } else {
            return $query;
        }
    }
}
