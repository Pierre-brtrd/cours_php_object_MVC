<?php

namespace App\Models;

use DateTime;

/**
 * Classe de Model de la table Poste
 */
class PosteModel extends Model
{
    /**
     * @var int
     */
    protected int $id;

    /**
     * @var string
     */
    protected string $titre;

    /**
     * @var string
     */
    protected string $description;

    /**
     * @var DateTime
     */
    protected Datetime $createdDAt;

    /**
     * @var bool
     */
    protected bool $actif;

    /**
     * @var int
     */
    protected int $userId;

    /**
     * @var string|null
     */
    protected ?string $image;

    public function __construct()
    {
        $this->table = 'poste';
    }

    /**
     * Cherche les postes avec les auteurs
     *
     * @return mixed
     */
    public function findActiveWithAuthor(): mixed
    {
        return $this->runQuery("SELECT p.*, u.nom, u.prenom, u.email  FROM $this->table p INNER JOIN user u ON p.userId = u.id WHERE actif = ?", [true])->fetchAll();
    }

    /**
     * Chercher tous les articles actifs avec une limite
     *
     * @param integer $max
     * @return mixed
     */
    public function findActiveWithLimit(int $max): mixed
    {
        return $this->runQuery("SELECT * FROM $this->table WHERE actif = ? LIMIT ?", [true, $max])->fetchAll();
    }

    /**
     * Cherche un article avec l'auteur
     *
     * @param integer $id
     * @return mixed
     */
    public function findOneActiveWithAuthor(int $id): mixed
    {
        return $this->runQuery("SELECT p.*, u.nom, u.prenom, u.email  FROM $this->table p INNER JOIN user u ON p.userId = u.id WHERE p.id = ? AND p.actif = ?", [$id, true])->fetch();
    }

    /**
     * Get undocumented variable
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set undocumented variable
     *
     * @param int $id  Undocumented variable
     *
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of titre
     *
     * @return string
     */
    public function getTitre(): string
    {
        return $this->titre;
    }

    /**
     * Set the value of titre
     *
     * @public string $titre
     * @return self
     */
    public function setTitre($titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get the value of description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param string $description
     *
     * @return self
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of createdDAt
     *
     * @return DateTime
     */
    public function getCreatedDAt(): DateTime
    {
        return $this->createdDAt;
    }

    /**
     * Set the value of createdDAt
     *
     * @param DateTime $createdDAt
     *
     * @return self
     */
    public function setCreatedDAt(DateTime $createdDAt): self
    {
        $this->createdDAt = $createdDAt;

        return $this;
    }

    /**
     * Get the value of actif
     *
     * @return bool
     */
    public function getActif(): bool
    {
        return $this->actif;
    }

    /**
     * Set the value of actif
     *
     * @param bool $actif
     *
     * @return self
     */
    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Get the value of userId
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * Set the value of userId
     *
     * @param int $userId
     *
     * @return self
     */
    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get the value of image
     *
     * @return string
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * Set the value of image
     *
     * @param string $image
     *
     * @return self
     */
    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }
}
