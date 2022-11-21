<?php

namespace App\Models;

class UserModel extends Model
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $nom;

    /**
     * @var string
     */
    protected $prenom;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var array
     */
    protected $roles;

    public function __construct()
    {
        $class = str_replace(__NAMESPACE__ . '\\', '', __CLASS__);
        $this->table = strtolower(str_replace('Model', '', $class));
    }

    /**
     * Cherche un utilisateur par son email
     *
     * @param string $email
     * @return mixed
     */
    public function findOneByEmail(string $email)
    {
        return $this->runQuery("SELECT * FROM $this->table WHERE email = ?", [$email])->fetch();
    }

    /**
     * Créer la session de l'utilisateur
     *
     * @return void
     */
    public function setSession()
    {
        $_SESSION['user'] = [
            'id' => $this->id,
            'prenom' => $this->prenom,
            'nom' => $this->nom,
            'email' => $this->email,
            'roles' => json_decode((string) $this->roles),
        ];
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
     * @return array
     */
    public function getRoles(): array
    {
        $roles =  $this->roles;

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Set the value of roles
     *
     * @param array $roles
     *
     * @return self
     */
    public function setRoles(array $roles): self
    {
        $this->roles = json_encode($roles);

        return $this;
    }
}
