<?php

namespace App\Entity\User;

use App\Attribute\LoggedEntity;
use App\Entity\Role\Role;
use App\Entity\Ticket\Ticket;
use App\Entity\Ticket\TicketMessage;
use App\EventListener\EntityLogListener;
use App\Repository\User\UserRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ISerranoDev\EncryptBundle\Attribute\Hashed;
use ISerranoDev\EncryptBundle\EventListener\HashListener;
use App\Doctrine\UlidGenerator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'usuario')]
#[ORM\EntityListeners([HashListener::class])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
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

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'usuario_modificado_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?User $updatedUser;

    #[ORM\OneToMany(targetEntity: UserHasRole::class, mappedBy: 'user', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $roles;

    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'user', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private Collection $tickets;

    #[ORM\OneToMany(targetEntity: TicketMessage::class, mappedBy: 'user', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private Collection $ticketMessages;

    ////////////////////////////////////////////////////////////////
    /// Fields
    ////////////////////////////////////////////////////////////////

    #[ORM\Column(name:'email', length: 255, unique: true, nullable: false)]
    private string $email;

    /**
     * @var string The hashed password
     */
    #[ORM\Column(name:'contrasena', nullable: false)]
    private string $password;

    #[ORM\Column(name:'activo', nullable: false, options: ["default" => true])]
    private bool $enabled;

    #[ORM\Column(name: 'fecha_creacion', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'fecha_alta', type: 'datetime', nullable: true)]
    private ?DateTime $entryDate;

    #[ORM\Column(name: 'fecha_baja', type: 'datetime', nullable: true)]
    private ?DateTime $leavingDate;

    #[ORM\Column(name:'codigo_recuperacion', nullable: true)]
    private ?string $recoverCode;

    #[ORM\Column(name: 'fecha_expiracion_recuperacion', type: 'datetime', nullable: true)]
    private ?DateTime $recoverCodeExpiration;

    #[ORM\Column(name:'nombre', type: 'text', unique: false, nullable: true)]
    #[Hashed]
    private ?string $name;

    #[ORM\Column(name:'apellido_1', type: 'text', unique: false, nullable: true)]
    #[Hashed]
    private ?string $surname1;

    #[ORM\Column(name:'apellido_2', type: 'text', unique: false, nullable: true)]
    #[Hashed]
    private ?string $surname2;

    ////////////////////////////////////////////////////////////////
    /// Methods
    ////////////////////////////////////////////////////////////////

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->ticketMessages = new ArrayCollection();
        $this->createdAt = new DateTime();
        $this->entryDate = new DateTime();
        $this->enabled = true;
    }


    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return User
     */
    public function setId(int $id): User
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = [];

        /** @var UserHasRole $role */
        foreach ($this->roles as $role){
            $roles[] = $role->getRole()->getName();
        }

        return array_unique($roles);
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles());
    }

    public function getRolesLabel(): array
    {
        $roles = [];

        /** @var UserHasRole $role */
        foreach ($this->roles as $role){
            $roles[] = $role->getRole()->getLabel();
        }

        return array_unique($roles);
    }

    public function getRolesIds(): array
    {
        $roles = [];

        /** @var UserHasRole $role */
        foreach ($this->roles as $role){
            $roles[] = $role->getRole()->getId();
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = new ArrayCollection();

        foreach ($roles as $role){
            $userHasRole = (new UserHasRole())
                ->setUser($this)
                ->setRole($role)
            ;

            $this->roles->add($userHasRole);
        }

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return User
     */
    public function setEnabled(bool $enabled): User
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     * @return User
     */
    public function setCreatedAt(DateTime $createdAt): User
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getEntryDate(): ?DateTime
    {
        return $this->entryDate;
    }

    /**
     * @param DateTime|null $entryDate
     * @return User
     */
    public function setEntryDate(?DateTime $entryDate): User
    {
        $this->entryDate = $entryDate;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getLeavingDate(): ?DateTime
    {
        return $this->leavingDate;
    }

    /**
     * @param DateTime|null $leavingDate
     * @return User
     */
    public function setLeavingDate(?DateTime $leavingDate): User
    {
        $this->leavingDate = $leavingDate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRecoverCode(): ?string
    {
        return $this->recoverCode;
    }

    /**
     * @param string|null $recoverCode
     * @return User
     */
    public function setRecoverCode(?string $recoverCode): User
    {
        $this->recoverCode = $recoverCode;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getRecoverCodeExpiration(): ?DateTime
    {
        return $this->recoverCodeExpiration;
    }

    /**
     * @param DateTime|null $recoverCodeExpiration
     * @return User
     */
    public function setRecoverCodeExpiration(?DateTime $recoverCodeExpiration): User
    {
        $this->recoverCodeExpiration = $recoverCodeExpiration;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    /**
     * @param Collection $tickets
     * @return User
     */
    public function setTickets(Collection $tickets): User
    {
        $this->tickets = $tickets;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getTicketMessages(): Collection
    {
        return $this->ticketMessages;
    }

    /**
     * @param Collection $ticketMessages
     * @return User
     */
    public function setTicketMessages(Collection $ticketMessages): User
    {
        $this->ticketMessages = $ticketMessages;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return User
     */
    public function setName(?string $name): User
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSurname1(): ?string
    {
        return $this->surname1;
    }

    /**
     * @param string|null $surname1
     * @return User
     */
    public function setSurname1(?string $surname1): User
    {
        $this->surname1 = $surname1;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSurname2(): ?string
    {
        return $this->surname2;
    }

    /**
     * @param string|null $surname2
     * @return User
     */
    public function setSurname2(?string $surname2): User
    {
        $this->surname2 = $surname2;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getUpdatedUser(): ?User
    {
        return $this->updatedUser;
    }

    /**
     * @param User|null $updatedUser
     * @return User
     */
    public function setUpdatedUser(?User $updatedUser): User
    {
        $this->updatedUser = $updatedUser;
        return $this;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(Role::ROLE_ADMIN);
    }

    public function getFullName()
    {
        $fullName = '';

        if($this->getName()){
            $fullName .= $this->getName();
        }

        if($this->getSurname1()){
            if($fullName !== ''){
                $fullName .= ' ';
            }
            $fullName .= $this->getSurname1();
        }

        if($this->getSurname2()){
            if($fullName !== ''){
                $fullName .= ' ';
            }
            $fullName .= $this->getSurname2();
        }

        return $fullName;

    }

    public function toArray() : array
    {
        return [
            'id' => $this->getId(),
            'email' => $this->getEmail(),
            'enabled' => $this->isEnabled(),
            'createdAt' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'entryDate' => $this->getEntryDate()?->format('Y-m-d H:i:s'),
            'leavingDate' => $this->getLeavingDate()?->format('Y-m-d H:i:s'),
            'name' => $this->getName(),
            'surname1' => $this->getSurname1(),
            'surname2' => $this->getSurname2(),
            'roles' => $this->getRoles(),
        ];
    }
}
