<?php

namespace App\Controller\Ticket;


use App\Request\Ticket\ChangeStatusTicketRequest;
use App\Request\Ticket\CreateTicketMessageRequest;
use App\Request\Ticket\CreateTicketRequest;
use App\Request\Ticket\EditTicketRequest;
use App\Service\Ticket\TicketService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/incidencias')]
class TicketController extends AbstractController
{

    private TicketService $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    #[Route(path: '/', name: 'app_tickets_list')]
    public function list(): Response
    {
        return $this->ticketService->list();
    }

    #[Route(path: '/ver-detalles/{id}', name: 'app_tickets_render_show', methods: 'GET')]
    public function renderShow(int $id): Response
    {
        return $this->ticketService->show($id);
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

    #[Route(path: '/editar/{id}', name: 'app_tickets_render_edit', methods: 'GET')]
    public function renderEdit(int $id): Response
    {
        return $this->ticketService->renderEdit($id);
    }

    #[Route(path: '/editar/{id}', name: 'app_tickets_process_edit', methods: 'POST')]
    public function processEdit(int $id, EditTicketRequest $request): Response
    {
        return $this->ticketService->processEdit($id, $request);
    }

    #[Route(path: '/cambiar-estado/{id}', name: 'app_tickets_change_status', methods: 'POST')]
    public function closeTicket(int $id, ChangeStatusTicketRequest $request): Response
    {
        return $this->ticketService->changeStatus($id, $request);
    }

    #[Route(path: '/{id}/crear-mensaje', name: 'app_tickets_create_message', methods: 'POST')]
    public function createMessage(int $id, CreateTicketMessageRequest $request): Response
    {
        return $this->ticketService->createMessage($id, $request);
    }

    #[Route(path: '/eliminar/{id}', name: 'app_tickets_delete', methods: 'POST')]
    public function delete(int $id): Response
    {
        return $this->ticketService->delete($id);
    }


}
