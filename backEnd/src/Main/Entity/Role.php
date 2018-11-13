<?php

namespace Main\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="Main\Repository\RoleRepository")
 * @Table(name="roles")
 */
class Role
{
    const P_NAME = 'name';
    const P_PERMISSIONS = 'permissions';

    /**
     * @Id
     * @Column(type="integer", nullable=false, options={"unsigned": true})
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /** @Column(type="string", length=30, nullable=false, unique=true) */
    protected $name;

    /**
     * @var Permission[]
     * @ManyToMany(targetEntity="Permission")
     * @JoinTable(name="roles_permissions",
     *      joinColumns={@JoinColumn(name="role_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="permission_id", referencedColumnName="id")}
     * )
     */
    protected $permissions;

    public function __construct()
    {
        $this->permissions = new ArrayCollection();
    }

    public function getId(): int
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

    /**
     * @return Permission[]
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @param Permission[] $permissions
     * @return self
     */
    public function setPermissions(array $permissions): self
    {
        $this->permissions = $permissions;
        return $this;
    }
}
