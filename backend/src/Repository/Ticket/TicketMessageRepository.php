<?php

namespace App\Repository\Ticket;

use App\Entity\Log\ImportLog;
use App\Entity\Ticket\Ticket;
use App\Entity\Ticket\TicketMessage;
use App\Entity\User\User;
use Doctrine\ORM\EntityRepository;

/**
 * @extends EntityRepository<ImportLog>
 *
 *
 * @method TicketMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method TicketMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method TicketMessage[]    findAll()
 * @method TicketMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketMessageRepository extends EntityRepository
{
    public function createTicketMessage(
        Ticket $ticket,
        User $user,
        string $message
    ): void
    {
        $ticketMessage = (new TicketMessage())
            ->setUser($user)
            ->setTicket($ticket)
            ->setMessage($message)
        ;

        $this->_em->persist($ticketMessage);
        $this->_em->flush();
    }

    public function deleteTicketMessage(TicketMessage $ticketMessage): void
    {
        $this->_em->remove($ticketMessage);
        $this->_em->flush();
    }
}
