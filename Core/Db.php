<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Db Class qui gère la connexion en BDD
 */
class Db extends PDO
{
    // Instance unique de la class
    private static ?Db $instance = null;

    // Information de connexion en BDD
    private const DBHOST = 'db_php_POO_MVC';
    private const DBUSER = 'root';
    private const DBPASS = 'root';
    private const DBNAME = "demo_mvc";

    /**
     * Constructeur de la class Db qui gère la connexion en BDD
     */
    public function __construct()
    {
        // DSN de connexion
        $dsn = 'mysql:dbname=' . self::DBNAME . ';host=' . self::DBHOST;

        // On appelle le constructeur de la classe PDO
        try {
            parent::__construct($dsn, self::DBUSER, self::DBPASS);

            $this->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');
            $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    /**
     * Récupérer l'instance de connexion
     *
     * @return self
     */
    public static function getInstance(): self
    {
        // On vérifie si aucune instance n'existe pas déjà
        if (self::$instance === null) {
            // Si elle n'existe pas, on créé l'instance pour créer 
            // la connexion en BDD
            self::$instance = new Db();
        }

        // On renvoie l'instance (La connexion en BDD)
        return self::$instance;
    }
}
