<?php

namespace App\Entity\Ticket;


use App\Entity\User\User;
use App\Repository\Ticket\TicketMessageRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketMessageRepository::class)]
#[ORM\Table(name: 'ticket_messages')]
class TicketMessage
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

    #[ORM\ManyToOne(targetEntity: Ticket::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(name: 'ticket_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Ticket $ticket;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'ticketMessages')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    ////////////////////////////////////////////////////////////////
    /// Fields
    ////////////////////////////////////////////////////////////////

    #[ORM\Column(name:'message', type:'text', nullable: false)]
    private string $message;

    #[ORM\Column(name:'created_at', type:'datetime', nullable: false)]
    private DateTime $createdAt;

    ////////////////////////////////////////////////////////////////
    /// Methods
    ////////////////////////////////////////////////////////////////

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * @return Ticket
     */
    public function getTicket(): Ticket
    {
        return $this->ticket;
    }

    /**
     * @param Ticket $ticket
     * @return TicketMessage
     */
    public function setTicket(Ticket $ticket): TicketMessage
    {
        $this->ticket = $ticket;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
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
     * @return TicketMessage
     */
    public function setUser(User $user): TicketMessage
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return TicketMessage
     */
    public function setMessage(string $message): TicketMessage
    {
        $this->message = $message;
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
     * @return TicketMessage
     */
    public function setCreatedAt(DateTime $createdAt): TicketMessage
    {
        $this->createdAt = $createdAt;
        return $this;
    }


}
