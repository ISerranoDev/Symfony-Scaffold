<?php

namespace App\Entity\Role;

use App\Entity\User\UserHasRole;
use App\Repository\Role\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\Table(name: 'roles')]
class Role
{

    ////////////////////////////////////////////////////////////////
    /// Primary Key
    ////////////////////////////////////////////////////////////////

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    ////////////////////////////////////////////////////////////////
    /// Relationships
    ////////////////////////////////////////////////////////////////

    #[ORM\OneToMany(mappedBy: 'role', targetEntity: UserHasRole::class)]
    private Collection $users;

    ////////////////////////////////////////////////////////////////
    /// Fields
    ////////////////////////////////////////////////////////////////

    #[ORM\Column(name:'name', length: 180, unique: true, nullable: false)]
    private string $name;

    #[ORM\Column(name:'label', length: 180, unique: true, nullable: false)]
    private string $label;

    ////////////////////////////////////////////////////////////////
    /// Methods
    ////////////////////////////////////////////////////////////////

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }


    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Role
     */
    public function setId(int $id): Role
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Role
     */
    public function setName(string $name): Role
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return Role
     */
    public function setLabel(string $label): Role
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @param Collection $users
     * @return Role
     */
    public function setUsers(Collection $users): Role
    {
        $this->users = $users;
        return $this;
    }



}
