<?php

namespace App\Repository\Ticket;

use App\Entity\Log\ImportLog;
use App\Entity\Ticket\Ticket;
use App\Entity\User\User;
use App\Utils\Classes\FilterService;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends EntityRepository<ImportLog>
 *
 *
 * @method Ticket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ticket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ticket[]    findAll()
 * @method Ticket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketRepository extends ServiceEntityRepository
{

    private PaginatorInterface $paginator;

    public function __construct(
        ManagerRegistry $registry,
        PaginatorInterface $paginator
    )
    {
        $this->paginator = $paginator;
        parent::__construct($registry, Ticket::class);
    }

    public function list(FilterService $filterService): PaginationInterface
    {
        $query = $this->createQueryBuilder('t')
            ->select('t')
            ->leftJoin('t.messages', 'tm')
            ->leftJoin('t.user', 'u')
            ->addSelect('tm')
            ->addSelect('u')
        ;

        $this->addFilters($filterService, $query);
        $this->addOrders($filterService, $query);

        return $this->paginator->paginate(
            $query,
            $filterService->page,
            $filterService->limit
        );

    }

    public function addFilters(FilterService $filter, QueryBuilder $query): void
    {
        foreach ($filter->getFilters() as $key => $value){
            switch ($key){
                case 'closed':
                    $query->andWhere('t.closed = :closed')
                        ->setParameter('closed', $value)
                    ;
                    break;

                case 'user':
                    $query->andWhere('u.id = :user')
                        ->setParameter('user', $value)
                    ;
                    break;

                case 'user_info':
                    $query->andWhere('u.username LIKE :userInfo OR u.email LIKE :userInfo')
                        ->setParameter('userInfo', "%".$value."%")
                    ;
                    break;

                case 'title':
                    $query->andWhere('t.title LIKE :title')
                        ->setParameter('title', "%".$value."%")
                    ;
                    break;

            }
        }
    }

    public function addOrders(FilterService $filter, QueryBuilder $query): void
    {
        foreach ($filter->getOrders() as $key => $value){

            switch ($key){
                case 'user':
                    $query->addOrderBy('u.username', $value);
                    break;

                case 'title':
                    $query->addOrderBy('t.title', $value);
                    break;

                case 'closed':
                    $query->addOrderBy('t.closed', $value);
                    break;

                case 'created_at':
                    $query->addOrderBy('t.createdAt', $value);
                    break;

            }
        }

        $query->addOrderBy('t.createdAt', 'DESC');
    }

    public function createTicket(
        ?User $user,
        string $title,
        ?string $description
    ): void
    {
        $ticket = (new Ticket())
            ->setUser($user)
            ->setTitle($title)
            ->setDescription($description)
        ;

        $this->_em->persist($ticket);
        $this->_em->flush();
    }

    public function editTicket(
        Ticket $ticket,
        string $title,
        ?string $description
    ): void
    {
        $ticket
            ->setTitle($title)
            ->setDescription($description)
        ;

        $this->_em->persist($ticket);
        $this->_em->flush();
    }

    public function changeStatusTicket(Ticket $ticket, bool $closed): void
    {
        $ticket
            ->setClosed($closed)
        ;

        if($closed){
            $ticket->setClosedAt(new DateTime());
        }

        $this->_em->persist($ticket);
        $this->_em->flush();
    }

    public function deleteTicket(Ticket $ticket): void
    {
        $this->_em->remove($ticket);
        $this->_em->flush();
    }

}
