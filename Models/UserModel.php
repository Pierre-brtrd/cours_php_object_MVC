<?php

namespace App\Models;

class UserModel extends Model
{
    public function __construct(
        protected ?int $id = null,
        protected ?string $nom = null,
        protected ?string $prenom = null,
        protected ?string $email = null,
        protected ?string $password = null,
        protected ?array $roles = null
    ) {
        $class = str_replace(__NAMESPACE__ . '\\', '', __CLASS__);
        $this->table = strtolower(str_replace('Model', '', $class));
    }

    /**
     * Cherche un utilisateur par son email
     *
     * @param string $email
     * @return mixed
     */
    public function findOneByEmail(string $email): mixed
    {
        return $this->fetchHydrate(
            $this->runQuery("SELECT * FROM $this->table WHERE email = ?", [$email])->fetch()
        );
    }

    /**
     * Créer la session de l'utilisateur
     *
     * @return void
     */
    public function setSession(): void
    {
        $_SESSION['user'] = [
            'id' => $this->id,
            'prenom' => $this->prenom,
            'nom' => $this->nom,
            'email' => $this->email,
            'roles' => $this->roles,
        ];
    }

    public function isAuthor(): bool
    {
        $query = $this->runQuery("SELECT * FROM $this->table JOIN poste p ON $this->table.id = p.userId WHERE $this->table.id = ?", [$this->id])->fetch();

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the value of id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param int $id
     *
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of nom
     *
     * @return string
     */
    public function getNom(): string
    {
        return $this->nom;
    }

    /**
     * Set the value of nom
     *
     * @param string $nom
     *
     * @return self
     */
    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get the value of prenom
     *
     * @return string
     */
    public function getPrenom(): string
    {
        return $this->prenom;
    }

    /**
     * Set the value of prenom
     *
     * @param string $prenom
     *
     * @return self
     */
    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get the value of email
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @param string $email
     *
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of password
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @param string $password
     *
     * @return self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of roles
     *
     * @return ?array
     */
    public function getRoles(): ?array
    {
        $this->roles[] = "ROLE_USER";

        return array_unique($this->roles);
    }

    /**
     * Set the value of roles
     *
     * @param null|string|array $roles
     *
     * @return self
     */
    public function setRoles(null|string|array $roles): self
    {
        if (is_string($roles)) {
            $this->roles = json_decode($roles ?: '[]');
        } else {
            $this->roles = $roles;
        }

        return $this;
    }
}
