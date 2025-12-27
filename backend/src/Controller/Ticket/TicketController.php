<?php

namespace App\Controller\Ticket;


use App\Request\Ticket\ChangeStatusTicketRequest;
use App\Request\Ticket\CreateTicketMessageRequest;
use App\Request\Ticket\CreateTicketRequest;
use App\Request\Ticket\DeleteTicketRequest;
use App\Request\Ticket\EditTicketRequest;
use App\Request\Ticket\ShowTicketRequest;
use App\Service\Ticket\TicketService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/incidencias')]
class TicketController extends AbstractController
{


    public function __construct(
        private readonly TicketService $ticketService
    )
    {
    }

    #[Route(path: '/', name: 'app_tickets_list')]
    public function list(): Response
    {
        return $this->ticketService->list();
    }

    #[Route(path: '/ver-detalles/{id}', name: 'app_tickets_render_show', methods: 'GET')]
    public function renderShow(ShowTicketRequest $request): Response
    {
        return $this->ticketService->show($request);
    }

    #[Route(path: '/nueva', name: 'app_tickets_render_new', methods: 'GET')]
    public function renderNew(): Response
    {
        return $this->ticketService->renderNew();
    }

    #[Route(path: '/nueva', name: 'app_tickets_process_new', methods: 'POST')]
    public function processNew(CreateTicketRequest $request): Response
    {
        return $this->ticketService->processNew($request);
    }

    #[Route(path: '/editar/{id}', name: 'app_tickets_process_edit', methods: 'POST')]
    public function processEdit(EditTicketRequest $request): Response
    {
        return $this->ticketService->processEdit($request);
    }

    #[Route(path: '/cambiar-estado/{id}', name: 'app_tickets_change_status', methods: 'POST')]
    public function closeTicket(ChangeStatusTicketRequest $request): Response
    {
        return $this->ticketService->changeStatus($request);
    }

    #[Route(path: '/{id}/crear-mensaje', name: 'app_tickets_create_message', methods: 'POST')]
    public function createMessage(CreateTicketMessageRequest $request): Response
    {
        return $this->ticketService->createMessage($request);
    }

    #[Route(path: '/eliminar/{id}', name: 'app_tickets_delete', methods: 'POST')]
    public function delete(DeleteTicketRequest $request): Response
    {
        return $this->ticketService->delete($request);
    }


}
