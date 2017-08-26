<?php

namespace Main\Entity;

/**
 * @Entity(repositoryClass="Main\Repository\UserRepository") @Table(name="users")
 */
class User
{
    /** @Id @Column(type="integer") @GeneratedValue  */
    private $id;

    /** @Column(type="string") */
    private $name;

    /** @Column(type="string", length=20, unique=true) */
    private $login;

    /** @Column(type="string", length=255) */
    private $password;

    /** @Column(type="float") */
    private $balance;

    /** @OneToOne(targetEntity="UserLimit", mappedBy="user")*/
    private $userLimit;

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

    public function getUserLimit(): UserLimit
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
}
