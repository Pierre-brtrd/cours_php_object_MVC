<?php

namespace App\Models;

use App\Core\Db;

class Model extends Db
{
    // Table de la base donnée
    protected $table;

    // Instance de Db
    private $db;

    /**
     * Récupère tous les entrées d'une table
     *
     * @return array
     */
    public function findAll()
    {
        $query = $this->runQuery('SELECT * FROM ' . $this->table);
        return $query->fetchAll();
    }

    /**
     * Requête avec critères
     *
     * @param array $args
     * @return array
     */
    public function findBy(array $args)
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
    public function find(int $id)
    {
        return $this->runQuery("SELECT * FROM $this->table WHERE id = $id")->fetch();
    }

    /**
     * Créé une entrée dan une table de la base de données
     *
     * @return bool
     */
    public function create()
    {
        $champs = [];
        $inter = [];
        $valeurs = [];

        // On boucle pour éclater le tableau d'arguments
        foreach ($this as $champ => $valeur) {
            if ($valeur !== null && $champ != 'db' && $champ != 'table' && $champ != 'fetchMod') {
                $champs[] = $champ;
                $inter[] = "?";
                $valeurs[] = $valeur;
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
     * @return bool
     */
    public function update()
    {
        $champs = [];
        $valeurs = [];

        // On boucle pour éclater le tableau d'arguments
        foreach ($this as $champ => $valeur) {
            if ($valeur !== null && $champ != 'db' && $champ != 'table' && $champ != 'fetchMod') {
                $champs[] = "$champ = ?";
                $valeurs[] = $valeur;
            }
        }
        $valeurs[] = $this->id;

        // On transforme le tableu champs en string
        $listChamps = implode(', ', $champs);

        // On execute la requête
        return $this->runQuery("UPDATE $this->table SET $listChamps WHERE id = ?", $valeurs);
    }

    /**
     * Supprime une entrée de la base de données via un ID
     *
     * @param integer $id
     * @return bool
     */
    public function delete(int $id)
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
    public function runQuery(string $sql, array $attributs = null)
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
    public function hydrate($donnees)
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
