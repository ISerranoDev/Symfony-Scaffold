<?php

namespace App\EventListener;

use App\Entity\Ticket\Ticket;
use App\Repository\Ticket\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(KernelEvents::EXCEPTION)]
class ExceptionListener
{

    private \Doctrine\ORM\EntityRepository | TicketRepository $ticketRepository;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->ticketRepository = $entityManager->getRepository(Ticket::class);
    }

    public function __invoke(ExceptionEvent $event): void
    {
        // You get the exception object from the received event
        $exception = $event->getThrowable();


        $this->ticketRepository->createTicket(
            null,
            $exception->getMessage(),
            $exception->getTraceAsString()
        );

        $event->getRequest()->getSession()->getFlashBag()->add('error', 'Ha habido un error interno, se notificará al administrador.');

        // Customize your response object to display the exception details
        $response = new RedirectResponse($event->getRequest()->headers->get('referer'));
        // sends the modified response object to the event
        $event->setResponse($response);
        
    }

}