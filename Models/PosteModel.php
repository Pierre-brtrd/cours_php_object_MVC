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
    protected $id;

    /**
     * @var string
     */
    protected $titre;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var DateTime
     */
    protected $createdDAt;

    /**
     * @var int
     */
    protected $actif;

    /**
     * @var int
     */
    protected $userId;

    /**
     * @var string
     */
    protected $image;

    public function __construct()
    {
        $this->table = 'poste';
    }

    /**
     * Cherche les postes avec les auteurs
     *
     * @return void
     */
    public function findActiveWithAuthor(bool $actif)
    {
        return $this->runQuery("SELECT * FROM $this->table INNER JOIN user ON $this->table.user_id = user.id WHERE actif = ?", [$actif])->fetchAll();
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
     * @return int
     */
    public function getActif(): int
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
    public function setActif(int $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Get the value of user_id
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * Set the value of user_id
     *
     * @param int $user_id
     *
     * @return self
     */
    public function setUserId(int $userId): self
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get the value of image
     *
     * @return string
     */
    public function getImage(): string
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
