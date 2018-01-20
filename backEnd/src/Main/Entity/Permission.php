<?php

namespace Main\Entity;

/**
 * @Entity(repositoryClass="Main\Repository\PermissionRepository")
 * @Table(name="permissions")
 */
class Permission
{
    const P_NAME = 'name';

    /**
     * @Id
     * @Column(type="integer", nullable=false, options={"unsigned": true})
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @Column(type="string", length=50, nullable=false, unique=true) */
    protected $name;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
}
