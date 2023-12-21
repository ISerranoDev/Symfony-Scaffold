<?php

namespace App\Entity\Ticket;


use App\Entity\User\User;
use App\Repository\Ticket\TicketRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[ORM\Table(name: 'tickets')]
class Ticket
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

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tickets')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?User $user;

    #[ORM\OneToMany(mappedBy: 'ticket', targetEntity: TicketMessage::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $messages;

    ////////////////////////////////////////////////////////////////
    /// Fields
    ////////////////////////////////////////////////////////////////

    #[ORM\Column(name:'title', type: 'text', unique: false, nullable: false)]
    private string $title;

    #[ORM\Column(name:'message', type:'text', nullable: false)]
    private string $description;

    #[ORM\Column(name:'closed', type:'boolean', nullable: false, options: ['default' => false])]
    private bool $closed;

    #[ORM\Column(name:'created_at', type:'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name:'closed_at', type:'datetime', nullable: true)]
    private ?DateTime $closedAt;

    ////////////////////////////////////////////////////////////////
    /// Methods
    ////////////////////////////////////////////////////////////////

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->closed = false;
        $this->createdAt = new DateTime();
    }


    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return Ticket
     */
    public function setUser(?User $user): Ticket
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    /**
     * @param Collection $messages
     * @return Ticket
     */
    public function setMessages(Collection $messages): Ticket
    {
        $this->messages = $messages;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Ticket
     */
    public function setTitle(string $title): Ticket
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Ticket
     */
    public function setDescription(string $description): Ticket
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this->closed;
    }

    /**
     * @param bool $closed
     * @return Ticket
     */
    public function setClosed(bool $closed): Ticket
    {
        $this->closed = $closed;
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
     * @return Ticket
     */
    public function setCreatedAt(DateTime $createdAt): Ticket
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getClosedAt(): ?DateTime
    {
        return $this->closedAt;
    }

    /**
     * @param DateTime|null $closedAt
     * @return Ticket
     */
    public function setClosedAt(?DateTime $closedAt): Ticket
    {
        $this->closedAt = $closedAt;
        return $this;
    }



}
