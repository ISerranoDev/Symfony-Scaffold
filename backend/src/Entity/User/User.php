<?php

namespace App\Entity\User;

use App\Entity\Ticket\Ticket;
use App\Entity\Ticket\TicketMessage;
use App\Repository\User\UserRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
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

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserHasRole::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $roles;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Ticket::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private Collection $tickets;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: TicketMessage::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private Collection $ticketMessages;

    ////////////////////////////////////////////////////////////////
    /// Fields
    ////////////////////////////////////////////////////////////////

    #[ORM\Column(name:'username', length: 180, unique: true, nullable: false)]
    private string $username;

    #[ORM\Column(name:'email', length: 180, unique: true, nullable: false)]
    private string $email;

    /**
     * @var string The hashed password
     */
    #[ORM\Column(name:'password', nullable: false)]
    private string $password;

    #[ORM\Column(name:'enabled', nullable: false, options: ["default" => true])]
    private bool $enabled;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'entry_date', type: 'datetime', nullable: true)]
    private ?DateTime $entryDate;

    #[ORM\Column(name: 'leaving_date', type: 'datetime', nullable: true)]
    private ?DateTime $leavingDate;

    #[ORM\Column(name:'recover_code', nullable: true)]
    private ?string $recoverCode;

    #[ORM\Column(name: 'recover_expiration_date', type: 'datetime', nullable: true)]
    private ?DateTime $recoverCodeExpiration;

    #[ORM\Column(name:'name', length: 180, unique: false, nullable: true)]
    private ?string $name;

    #[ORM\Column(name:'surname_1', length: 180, unique: false, nullable: true)]
    private ?string $surname1;

    #[ORM\Column(name:'surname_2', length: 180, unique: false, nullable: true)]
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


    public function getId(): int
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
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return User
     */
    public function setUsername(string $username): User
    {
        $this->username = $username;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->username;
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

    public function hasRole(string $role){
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
     * @return Collection
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    /**
     * @param Collection $addresses
     * @return User
     */
    public function setAddresses(Collection $addresses): User
    {
        $this->addresses = $addresses;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getPhones(): Collection
    {
        return $this->phones;
    }

    /**
     * @param Collection $phones
     * @return User
     */
    public function setPhones(Collection $phones): User
    {
        $this->phones = $phones;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getEmails(): Collection
    {
        return $this->emails;
    }

    /**
     * @param Collection $emails
     * @return User
     */
    public function setEmails(Collection $emails): User
    {
        $this->emails = $emails;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    /**
     * @param Collection $cards
     * @return User
     */
    public function setCards(Collection $cards): User
    {
        $this->cards = $cards;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getVCards(): Collection
    {
        return $this->vCards;
    }

    /**
     * @param Collection $vCards
     * @return User
     */
    public function setVCards(Collection $vCards): User
    {
        $this->vCards = $vCards;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }

    /**
     * @param Collection $jobs
     * @return User
     */
    public function setJobs(Collection $jobs): User
    {
        $this->jobs = $jobs;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getWebpages(): Collection
    {
        return $this->webpages;
    }

    /**
     * @param Collection $webpages
     * @return User
     */
    public function setWebpages(Collection $webpages): User
    {
        $this->webpages = $webpages;
        return $this;
    }





}
