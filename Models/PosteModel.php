<?php

namespace App\Models;

use DateTime;

/**
 * Classe de Model de la table Poste
 */
class PosteModel extends Model
{
    public function __construct(
        protected ?int $id = null,
        protected ?string $titre = null,
        protected ?string $description = null,
        protected ?Datetime $created_at = null,
        protected ?bool $actif = null,
        protected ?int $userId = null,
        protected ?string $image = null
    ) {
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
        return $this->fetchHydrate(
            $this->runQuery("SELECT * FROM $this->table WHERE actif = :active LIMIT :max OFFSET 0", ['active' => true, 'max' => $max])->fetchAll()
        );
    }

    public function findAllWithPagination(int $maxPerPage, int $page = 1, bool $actif = false): array
    {
        $sql = "SELECT p.*, u.nom, u.prenom, u.email FROM $this->table p INNER JOIN user u ON p.userId = u.id";

        if ($actif) {
            $sql .= " WHERE actif = :actif";
        }

        $sql .= " ORDER BY created_at DESC";

        $totalPage = ceil($this->runQuery($sql, $actif ? ['actif' => true] : null)->rowCount() / $maxPerPage);

        return [
            'pages' => $totalPage,
            'postes' => $this->paginate($sql, $maxPerPage, $page, $actif ? ['actif' => true] : null)
        ];
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
    public function getCreated_at(): DateTime
    {
        return $this->created_at;
    }

    /**
     * Set the value of createdDAt
     *
     * @param DateTime $createdDAt
     *
     * @return self
     */
    public function setCreated_at(null|DateTime|string $createdAt): self
    {
        if (is_string($createdAt)) {
            $createdAt = new \DateTime($createdAt);
        }
        $this->created_at = $createdAt;

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
     * @return ?string
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * Set the value of image
     *
     * @param ?array $image
     *
     * @return self
     */
    public function setImage(null|array|string $image, bool $remove = false): self
    {
        if ($image && is_array($image)) {
            $imageName = $this->uploadImage($image, $this->image ? true : $remove);
        } elseif (is_string($image)) {
            $imageName = $image;
        } else {
            $imageName = null;
        }

        $this->image = $imageName;

        return $this;
    }
}
