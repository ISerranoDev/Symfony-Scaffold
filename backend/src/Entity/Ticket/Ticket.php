<?php

namespace App\Entity\Ticket;


use App\Entity\User\User;
use App\Repository\Ticket\TicketRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Doctrine\UlidGenerator;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[ORM\Table(name: 'tickets')]
class Ticket
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

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tickets')]
    #[ORM\JoinColumn(name: 'usuario_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?User $user;

    #[ORM\OneToMany(targetEntity: TicketMessage::class, mappedBy: 'ticket', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $messages;

    ////////////////////////////////////////////////////////////////
    /// Fields
    ////////////////////////////////////////////////////////////////

    #[ORM\Column(name:'titulo', type: 'text', unique: false, nullable: false)]
    private string $title;

    #[ORM\Column(name:'descripcion', type:'text', nullable: false)]
    private string $description;

    #[ORM\Column(name:'cerrado', type:'boolean', nullable: false, options: ['default' => false])]
    private bool $closed;

    #[ORM\Column(name:'fecha_creacion', type:'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name:'fecha_cerrado', type:'datetime', nullable: true)]
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


    public function getId(): string
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
