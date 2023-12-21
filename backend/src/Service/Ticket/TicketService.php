<?php

namespace App\Service\Ticket;


use App\Entity\Ticket\Ticket;
use App\Entity\Ticket\TicketMessage;
use App\Repository\Ticket\TicketMessageRepository;
use App\Repository\Ticket\TicketRepository;
use App\Request\Ticket\ChangeStatusTicketRequest;
use App\Request\Ticket\CreateTicketMessageRequest;
use App\Request\Ticket\CreateTicketRequest;
use App\Request\Ticket\EditTicketRequest;
use App\Utils\AbstractClasses\AbstractService;
use App\Utils\Classes\FilterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class TicketService extends AbstractService
{

    private \Doctrine\ORM\EntityRepository | TicketRepository $ticketRepository;
    private \Doctrine\ORM\EntityRepository | TicketMessageRepository $ticketMessageRepository;
    private FilterService $filterService;

    public function __construct(
        EntityManagerInterface $entityManager,
        FilterService $filterService
    )
    {
        $this->ticketRepository = $entityManager->getRepository(Ticket::class);
        $this->ticketMessageRepository = $entityManager->getRepository(TicketMessage::class);

        $this->filterService = $filterService;

    }

    public function list(): Response
    {

        if(!$this->getUser()->hasRole('ROLE_ADMIN')){

            $this->filterService->addFilter('user', $this->getUser()->getId());
        }

        $tickets = $this->ticketRepository->list($this->filterService);

        return $this->render('ticket/index.html.twig', [
            'tickets' => $tickets,
            'totalTickets' => $tickets->getTotalItemCount(),
            'filterService' => $this->filterService,
        ]);
    }

    public function renderNew(): Response
    {
        return $this->render('ticket/new.html.twig', [
        ]);
    }

    public function renderEdit(int $ticketId): Response
    {
        $ticket = $this->ticketRepository->find($ticketId);

        if(!$this->getUser()->hasRole('ROLE_ADMIN') && $ticket->getUser()->getId() != $this->getUser()->getId()){
            $this->addFlash('error', 'no tienes permiso para editar esta incidencia');
            return $this->redirectBack();
        }

        return $this->render('ticket/edit.html.twig', [
            'ticket' => $ticket
        ]);
    }

    public function show(int $ticketId): Response
    {

        $ticket = $this->ticketRepository->find($ticketId);

        if(!$this->getUser()->hasRole('ROLE_ADMIN') && $ticket->getUser()->getId() != $this->getUser()->getId()){
            $this->addFlash('error', 'no tienes permiso para ver esta incidencia');
            return $this->redirectBack();
        }

        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket
        ]);
    }

    public function processNew(CreateTicketRequest $request): Response
    {
        if(!$request->isValid()){
            $this->addFlash('error', 'No se ha podido crear la incidencia');
            return $this->redirectToRoute('app_tickets_render_new', ['errors' => $request->getErrors()]);
        }

        if($this->isCsrfTokenValid('create-ticket', $this->getRequest()->request->get('_csrf_token'))){

            $this->ticketRepository->createTicket(
                $this->getUser(),
                $request->getAttribute('title'),
                $request->getAttribute('description')
            );

            $this->addFlash('success', 'Se ha creado la incidencia satisfactoriamente');

        }

        return $this->redirectToRoute('app_tickets_list');
    }

    public function processEdit(int $ticketId, EditTicketRequest $request): Response
    {
        if(!$request->isValid()){
            $this->addFlash('error', 'No se ha podido editar la incidencia');
            return $this->redirectToRoute('app_tickets_render_edit', ['id' => $ticketId, 'errors' => $request->getErrors()]);
        }

        $ticket = $this->ticketRepository->find($ticketId);

        if(!$this->getUser()->hasRole('ROLE_ADMIN') && $ticket->getUser()->getId() != $this->getUser()->getId()){
            $this->addFlash('error', 'No tienes permiso para ver esta incidencia');
            return $this->redirectBack();
        }

        if($this->isCsrfTokenValid('edit-ticket', $this->getRequest()->request->get('_csrf_token'))){

            $this->ticketRepository->editTicket(
                $ticket,
                $request->getAttribute('title'),
                $request->getAttribute('description')
            );

            $this->addFlash('success', 'Se ha editado la incidencia satisfactoriamente');

        }

        return $this->redirectToRoute('app_tickets_list');
    }

    public function changeStatus(int $ticketId, ChangeStatusTicketRequest $request): Response
    {
        if(!$request->isValid()){
            $this->addFlash('error', 'No se ha podido cambiar el estado de la incidencia');
            return $this->redirectBack(302, ['errors' => $request->getErrors()]);
        }

        $ticket = $this->ticketRepository->find($ticketId);


        if($this->isCsrfTokenValid('change-status-ticket', $this->getRequest()->request->get('_csrf_token'))){

            $this->ticketRepository->changeStatusTicket(
                $ticket,
                $request->getAttribute('closed')
            );

            $this->addFlash('success', 'Se ha cambiado el estado de la incidencia satisfactoriamente');

        }

        return $this->redirectBack();
    }

    public function createMessage(int $ticketId, CreateTicketMessageRequest $request): Response
    {
        if(!$request->isValid()){
            $this->addFlash('error', 'No se ha podido crear el mensaje');
            return $this->redirectToRoute('app_tickets_render_show', ['id' => $ticketId, 'errors' => $request->getErrors()]);
        }

        $ticket = $this->ticketRepository->find($ticketId);

        if(!$this->getUser()->hasRole('ROLE_ADMIN') && $ticket->getUser()->getId() != $this->getUser()->getId()){
            $this->addFlash('error', 'No tienes permiso para crear un mensaje en esta incidencia');
            return $this->redirectBack();
        }

        if($this->isCsrfTokenValid('create-ticket-message', $this->getRequest()->request->get('_csrf_token'))){

            $this->ticketMessageRepository->createTicketMessage(
                $ticket,
                $this->getUser(),
                $request->getAttribute('message')
            );

            $this->addFlash('success', 'Se ha creado el mensaje satisfactoriamente');

        }

        return $this->redirectToRoute('app_tickets_render_show', ['id' => $ticket->getId()]);
    }

    public function delete(int $ticketId): Response
    {

        $ticket = $this->ticketRepository->find($ticketId);

        if(!$this->getUser()->hasRole('ROLE_ADMIN') && $ticket->getUser()->getId() != $this->getUser()->getId()){
            $this->addFlash('error', 'No tienes permiso para eliminar incidencia');
            return $this->redirectBack();
        }

        if($this->isCsrfTokenValid('delete-ticket', $this->getRequest()->request->get('_csrf_token'))){

            $this->ticketRepository->deleteTicket(
                $ticket
            );

            $this->addFlash('success', 'Se ha eliminado la incidencia satisfactoriamente');

        }

        return $this->redirectToRoute('app_tickets_list');
    }


}
