<?php

namespace Main\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="Main\Repository\UserRepository")
 * @Table(name="users")
 */
class User implements \JsonSerializable
{
    const P_NAME = 'name';
    const P_LOGIN = 'login';
    const P_PASSWORD = 'password';
    const P_BALANCE = 'balance';
    const P_LANG = 'lang';
    const L_USER_LIMIT = 'userLimit';

    /**
     * @Id
     * @Column(type="integer", nullable=false, options={"unsigned": true})
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /** @Column(type="string", length=30, nullable=false) */
    protected $name = '';

    /** @Column(type="string", length=20, nullable=false, unique=true) */
    protected $login = '';

    /** @Column(type="string", length=255, nullable=false) */
    protected $password = '';

    /** @Column(type="decimal", precision=8, scale=2, nullable=false) */
    protected $balance = 0.0;

    /** @Column(type="string", length=3, nullable=false) */
    protected $lang = '';

    /** @OneToOne(targetEntity="UserLimit", mappedBy="user", cascade={"persist", "remove"}) */
    protected $userLimit;

    /**
     * @var Role[]
     * @ManyToMany(targetEntity="Role")
     * @JoinTable(name="users_roles",
     *      joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    protected $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): self
    {
        $this->balance = $balance;
        return $this;
    }

    public function getUserLimit(): ?UserLimit
    {
        return $this->userLimit;
    }

    public function setUserLimit(UserLimit $userLimit): self
    {
        $this->userLimit = $userLimit;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;
        return $this;
    }

    public function getLang(): string
    {
        return $this->lang;
    }

    public function setLang(string $lang): self
    {
        $this->lang = $lang;
        return $this;
    }

    /**
     * @return Role[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param Role[] $roles
     * @return self
     */
    public function setRoles($roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'login' => $this->getLogin(),
            'lang' => $this->getLang(),
        ];
    }
}
