<?php

namespace App\Entity\User;

use App\Entity\Role\Role;
use Doctrine\ORM\Mapping as ORM;
use App\Doctrine\UlidGenerator;


#[ORM\Entity]
#[ORM\Table(name: 'usuario_has_roles')]
#[ORM\UniqueConstraint(name: "user_has_role_unique", columns: ["usuario_id", "rol_id"])]
class UserHasRole
{

    ////////////////////////////////////////////////////////////////
    /// Primary Key
    ////////////////////////////////////////////////////////////////

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 50, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    private string $id;

    ////////////////////////////////////////////////////////////////
    /// Relationships
    ////////////////////////////////////////////////////////////////

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'roles')]
    #[ORM\JoinColumn(name: 'usuario_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Role::class, inversedBy: 'users')]
    #[ORM\JoinColumn(name: 'rol_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Role $role;

    ////////////////////////////////////////////////////////////////
    /// Fields
    ////////////////////////////////////////////////////////////////

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return UserHasRole
     */
    public function setId(string $id): UserHasRole
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return UserHasRole
     */
    public function setUser(User $user): UserHasRole
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return Role
     */
    public function getRole(): Role
    {
        return $this->role;
    }

    /**
     * @param Role $role
     * @return UserHasRole
     */
    public function setRole(Role $role): UserHasRole
    {
        $this->role = $role;
        return $this;
    }
}
