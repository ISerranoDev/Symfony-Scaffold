<?php

namespace App\Service\Ticket;


use App\Entity\Role\Role;
use App\Entity\Ticket\Ticket;
use App\Entity\Ticket\TicketMessage;
use App\Repository\Ticket\TicketMessageRepository;
use App\Repository\Ticket\TicketRepository;
use App\Request\Ticket\ChangeStatusTicketRequest;
use App\Request\Ticket\CreateTicketMessageRequest;
use App\Request\Ticket\CreateTicketRequest;
use App\Request\Ticket\DeleteTicketRequest;
use App\Request\Ticket\EditTicketRequest;
use App\Request\Ticket\ShowTicketRequest;
use App\Utils\AbstractClasses\AbstractService;
use App\Utils\Classes\FilterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class TicketService extends AbstractService
{

    private \Doctrine\ORM\EntityRepository | TicketRepository $ticketRepository;
    private \Doctrine\ORM\EntityRepository | TicketMessageRepository $ticketMessageRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        private readonly FilterService $filterService
    )
    {
        $this->ticketRepository = $entityManager->getRepository(Ticket::class);
        $this->ticketMessageRepository = $entityManager->getRepository(TicketMessage::class);

    }

    public function list(): Response
    {

        if(!$this->getUser()->hasRole(Role::ROLE_ADMIN)){

            $this->filterService->addFilter('user', $this->getUser()->getId());
        }

        $tickets = $this->ticketRepository->list($this->filterService);

        return $this->render('ticket/index.html.twig', [
            'tickets' => $tickets,
            'totalTickets' => $tickets->getTotalItemCount(),
            'filterService' => $this->filterService,
            'lastPage' => ceil($tickets->getTotalItemCount() / $tickets->getItemNumberPerPage())
        ]);
    }

    public function renderNew(): Response
    {
        return $this->render('ticket/new.html.twig', [
        ]);
    }

    public function renderEdit(ShowTicketRequest $request): Response
    {
        $ticket = $this->ticketRepository->find($request->getAttribute('id'));

        if(!$this->getUser()->hasRole(Role::ROLE_ADMIN) && $ticket->getUser()->getId() != $this->getUser()->getId()){
            $this->addFlash('error', 'no tienes permiso para editar esta incidencia');
            return $this->redirectBack();
        }

        return $this->render('ticket/edit.html.twig', [
            'ticket' => $ticket
        ]);
    }

    public function show(ShowTicketRequest $request): Response
    {

        $ticket = $this->ticketRepository->find($request->getAttribute('id'));

        if(!$this->getUser()->hasRole(Role::ROLE_ADMIN) && $ticket->getUser()->getId() != $this->getUser()->getId()){
            $this->addFlash('error', 'no tienes permiso para ver esta incidencia');
            return $this->redirectBack();
        }

        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket
        ]);
    }

    public function processNew(CreateTicketRequest $request): Response
    {

        $this->ticketRepository->createTicket(
            $this->getUser(),
            $request->getAttribute('title'),
            $request->getAttribute('description')
        );

        $this->addFlash('success', 'Se ha creado la incidencia satisfactoriamente');

        return $this->redirectToRoute('app_tickets_list');
    }

    public function processEdit(EditTicketRequest $request): Response
    {

        $ticket = $this->ticketRepository->find($request->getAttribute('id'));

        if(!$this->getUser()->hasRole(Role::ROLE_ADMIN) && $ticket->getUser()->getId() != $this->getUser()->getId()){
            $this->addFlash('error', 'No tienes permiso para ver esta incidencia');
            return $this->redirectBack();
        }

        $this->ticketRepository->editTicket(
            $ticket,
            $request->getAttribute('title'),
            $request->getAttribute('description')
        );

        $this->addFlash('success', 'Se ha editado la incidencia satisfactoriamente');

        return $this->redirectToRoute('app_tickets_list');
    }

    public function changeStatus(ChangeStatusTicketRequest $request): Response
    {

        $ticket = $this->ticketRepository->find($request->getAttribute('id'));

        $this->ticketRepository->changeStatusTicket(
            $ticket,
            $request->getAttribute('closed')
        );

        $this->addFlash('success', 'Se ha cambiado el estado de la incidencia satisfactoriamente');

        return $this->redirectBack();
    }

    public function createMessage(CreateTicketMessageRequest $request): Response
    {

        $ticket = $this->ticketRepository->find($request->getAttribute('id'));

        if(!$this->getUser()->hasRole(Role::ROLE_ADMIN) && $ticket->getUser()->getId() != $this->getUser()->getId()){
            $this->addFlash('error', 'No tienes permiso para crear un mensaje en esta incidencia');
            return $this->redirectBack();
        }

        $this->ticketMessageRepository->createTicketMessage(
            $ticket,
            $this->getUser(),
            $request->getAttribute('message')
        );

        $this->addFlash('success', 'Se ha creado el mensaje satisfactoriamente');


        return $this->redirectToRoute('app_tickets_render_show', ['id' => $ticket->getId()]);
    }

    public function delete(DeleteTicketRequest $request): Response
    {

        $ticket = $this->ticketRepository->find($request->getAttribute('id'));

        if(!$this->getUser()->hasRole(Role::ROLE_ADMIN) && $ticket->getUser()->getId() != $this->getUser()->getId()){
            $this->addFlash('error', 'No tienes permiso para eliminar incidencia');
            return $this->redirectBack();
        }

        $this->ticketRepository->deleteTicket(
            $ticket
        );

        $this->addFlash('success', 'Se ha eliminado la incidencia satisfactoriamente');

        return $this->redirectToRoute('app_tickets_list');
    }


}
