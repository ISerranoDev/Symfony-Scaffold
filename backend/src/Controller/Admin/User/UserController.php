<?php

namespace App\Controller\Admin\User;


use App\Attribute\AdminRoute;
use App\Request\Admin\User\CreateUserRequest;
use App\Request\Admin\User\DeleteUserRequest;
use App\Request\Admin\User\EditUserRequest;
use App\Request\Admin\User\ShowUserRequest;
use App\Service\Admin\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[AdminRoute(path: '/usuarios')]
class UserController extends AbstractController
{

    public function __construct(
        private readonly UserService $userService
    )
    {
    }

    #[Route(path: '/', name: 'admin_users_list')]
    public function list(): Response
    {
        return $this->userService->list();
    }

    #[Route(path: '/nuevo', name: 'admin_users_render_new', methods: 'GET')]
    public function renderNew(): Response
    {
        return $this->userService->renderNew();
    }

    #[Route(path: '/nuevo', name: 'admin_users_process_new', methods: 'POST')]
    public function processNew(CreateUserRequest $request): Response
    {
        return $this->userService->processNew($request);
    }

    #[Route(path: '/ver-detalles/{id}', name: 'admin_users_render_show', methods: 'GET')]
    public function renderShow(ShowUserRequest $request): Response
    {
        return $this->userService->renderShow($request);
    }

    #[Route(path: '/editar/{id}', name: 'admin_users_process_edit', methods: 'POST')]
    public function processEdit(EditUserRequest $request): Response
    {
        return $this->userService->processEdit($request);
    }

    #[Route(path: '/eliminar/{id}', name: 'admin_users_delete', methods: 'POST')]
    public function delete(DeleteUserRequest $request): Response
    {
        return $this->userService->delete($request);
    }


}
