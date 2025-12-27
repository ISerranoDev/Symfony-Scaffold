<?php

namespace App\EventListener;

use App\Entity\Ticket\Ticket;
use App\Kernel;
use App\Repository\Ticket\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(KernelEvents::EXCEPTION)]
class ExceptionListener
{

    private \Doctrine\ORM\EntityRepository | TicketRepository $ticketRepository;
    private string $env;

    public function __construct(
        ParameterBagInterface $parameterBag,
        EntityManagerInterface $entityManager,
        private readonly ManagerRegistry $doctrine
    )
    {
        $this->ticketRepository = $entityManager->getRepository(Ticket::class);
        $this->env = strtolower($parameterBag->get('app-env'));
    }

    public function __invoke(ExceptionEvent $event): void
    {


        // You get the exception object from the received event
        $exception = $event->getThrowable();

        if(
            in_array($exception->getCode() ,[404, 403]) ||
            str_contains($exception->getMessage(), 'No route found for') ||
            $exception instanceof NotFoundHttpException ||
            $exception instanceof FileNotFoundException
        ){
            return;
        }

        if($this->env === Kernel::PROD_ENV){
            $this->doctrine->resetManager();
            $this->ticketRepository->createTicket(
                null,
                $exception->getMessage(),
                $exception->getTraceAsString()
            );

            $event->getRequest()->getSession()->getFlashBag()->add('error', 'Ha habido un error interno, se notificará al administrador.');

            $url = $event->getRequest()->headers->get('referer') ?? '/';

            // Customize your response object to display the exception details
            $response = new RedirectResponse($url);
            // sends the modified response object to the event
            $event->setResponse($response);
        }

    }

}
