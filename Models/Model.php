<?php

namespace App\Models;

use App\Core\Db;
use PDOStatement;

class Model extends Db
{
    /**
     * Table de la base donnée
     *
     * @var string
     */
    protected string $table;

    protected ?string $className = null;

    /**
     * Instance de Db
     *
     * @var Db
     */
    private Db $db;

    /**
     * Récupère tous les entrées d'une table
     *
     * @return array
     */
    public function findAll(): array
    {
        $query = $this->runQuery('SELECT * FROM ' . $this->table);
        return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    /**
     * Requête avec critères
     *
     * @param array $args
     * @return array
     */
    public function findBy(array $args): array
    {
        $champs = [];
        $valeurs = [];

        // On boucle pour éclater le tableau d'arguments
        foreach ($args as $champ => $valeur) {
            $champs[] = "$champ = ?";
            $valeurs[] = $valeur;
        }

        // On transforme le tableu champs en string
        $listChamps = implode(' AND ', $champs);

        // On execute la requête
        return $this->runQuery("SELECT * FROM $this->table WHERE $listChamps", $valeurs)->fetchAll();
    }

    /**
     * Requête avec recherche par id
     *
     * @param integer $id
     * @return mixed
     */
    public function find(int $id): mixed
    {
        return $this->runQuery("SELECT * FROM $this->table WHERE id = $id")->fetch();
    }

    /**
     * Créé une entrée dan une table de la base de données
     *
     * @return PDOStatement|bool
     */
    public function create(): PDOStatement|bool
    {
        $champs = [];
        $inter = [];
        $valeurs = [];

        // On boucle pour éclater le tableau d'arguments
        foreach ($this as $champ => $valeur) {
            if ($valeur !== null && $champ != 'db' && $champ != 'table' && $champ != 'fetchMod') {
                $champs[] = $champ;
                $inter[] = ":$champ";
                $valeurs[$champ] = is_array($valeur) ? json_encode($valeur) : $valeur;
            }
        }

        // On transforme le tableu champs en string
        $listChamps = implode(', ', $champs);
        $listInter = implode(', ', $inter);

        // On execute la requête
        return $this->runQuery("INSERT INTO $this->table ($listChamps) VALUES ($listInter)", $valeurs);
    }

    /**
     * Mettre à jour une entrée de la base de données
     *
     * @return PDOStatement|bool
     */
    public function update(int $id): PDOStatement|bool
    {
        $champs = [];
        $valeurs = [];

        // On boucle pour éclater le tableau d'arguments
        foreach ($this as $champ => $valeur) {
            if ($valeur !== null && $champ != 'db' && $champ != 'table' && $champ != 'fetchMod' && $champ != 'id') {
                $champs[] = "$champ = :$champ";
                $valeurs[$champ] = is_array($valeur) ? json_encode($valeur) : $valeur;
            }
        }

        $valeurs['id'] = $id;

        // On transforme le tableu champs en string
        $listChamps = implode(', ', $champs);

        // On execute la requête
        return $this->runQuery("UPDATE $this->table SET $listChamps WHERE id = :id", $valeurs);
    }

    /**
     * Supprime une entrée de la base de données via un ID
     *
     * @param integer $id
     * @return PDOStatement|bool
     */
    public function delete(int $id): PDOStatement|bool
    {
        return $this->runQuery("DELETE FROM $this->table WHERE id = ?", [$id]);
    }

    /**
     * Fonction pour lancer les requêtes en base de données
     *
     * @param string $sql
     * @param array|null $attributs
     * @return PDOStatement|false
     */
    public function runQuery(string $sql, array $attributs = null): ?PDOStatement
    {
        // On récupère l'instance de DB
        $this->db = Db::getInstance();

        // On vérifie si on a des attributs
        if ($attributs !== null) {
            // Requête préparée
            $query = $this->db->prepare($sql);
            $query->execute($attributs);
            return $query;
        } else {
            // Requête simple
            return $this->db->query($sql);
        }
    }

    /**
     * Création d'objet par hydratation d'un tableau associatif
     *
     * @param $donnees
     * @return Model
     */
    public function hydrate($donnees): self
    {
        foreach ($donnees as $key => $value) {
            // On récupère le nom du setter correspondant à la clé (key)
            $setter = 'set' . ucfirst($key);

            // On vérifie si le setter existe
            if (method_exists($this, $setter)) {
                // On appelle le setter
                $this->$setter($value);
            }
        }

        return $this;
    }
}
