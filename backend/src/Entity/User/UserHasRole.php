<?php

namespace App\Entity\User;

use App\Entity\Role\Role;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
#[ORM\Table(name: 'user_has_roles')]
#[ORM\UniqueConstraint(name: "user_has_role_unique", columns: ["user_id", "role_id"])]
class UserHasRole
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

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'roles')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Role::class, inversedBy: 'users')]
    #[ORM\JoinColumn(name: 'role_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Role $role;

    ////////////////////////////////////////////////////////////////
    /// Fields
    ////////////////////////////////////////////////////////////////

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return UserHasRole
     */
    public function setId(int $id): UserHasRole
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
