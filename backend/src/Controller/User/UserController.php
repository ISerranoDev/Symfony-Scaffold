<?php

namespace App\Controller\User;


use App\Request\User\CreateUserRequest;
use App\Request\User\EditUserRequest;
use App\Service\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/usuarios')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{

    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    #[Route(path: '/', name: 'app_users_list')]
    public function list(): Response
    {
        return $this->userService->list();
    }

    #[Route(path: '/nuevo', name: 'app_users_render_new', methods: 'GET')]
    public function renderNew(): Response
    {
        return $this->userService->renderNew();
    }

    #[Route(path: '/nuevo', name: 'app_users_process_new', methods: 'POST')]
    public function processNew(CreateUserRequest $request): Response
    {
        return $this->userService->processNew($request);
    }

    #[Route(path: '/ver-detalles/{id}', name: 'app_users_render_show', methods: 'GET')]
    public function renderShow(int $id): Response
    {
        return $this->userService->renderShow($id);
    }

    #[Route(path: '/editar/{id}', name: 'app_users_render_edit', methods: 'GET')]
    public function renderEdit(int $id): Response
    {
        return $this->userService->renderEdit($id);
    }

    #[Route(path: '/editar/{id}', name: 'app_users_process_edit', methods: 'POST')]
    public function processEdit(int $id, EditUserRequest $request): Response
    {
        return $this->userService->processEdit($id, $request);
    }

    #[Route(path: '/eliminar/{id}', name: 'app_users_delete', methods: 'POST')]
    public function delete(int $id): Response
    {
        return $this->userService->delete($id);
    }


}
