<?php

namespace App\Service\Admin\User;

use App\Entity\Role\Role;
use App\Entity\User\User;
use App\Repository\Role\RoleRepository;
use App\Repository\User\UserRepository;
use App\Request\Admin\User\CreateUserRequest;
use App\Request\Admin\User\DeleteUserRequest;
use App\Request\Admin\User\EditUserRequest;
use App\Request\Admin\User\ShowUserRequest;
use App\Utils\AbstractClasses\AbstractService;
use App\Utils\Classes\FilterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class UserService extends AbstractService
{

    private \Doctrine\ORM\EntityRepository | UserRepository $userRepository;
    private \Doctrine\ORM\EntityRepository | RoleRepository $roleRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        private readonly FilterService $filterService
    )
    {
        $this->userRepository = $entityManager->getRepository(User::class);
        $this->roleRepository = $entityManager->getRepository(Role::class);

    }

    public function list(): Response
    {
        $users = $this->userRepository->list($this->filterService);

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'totalResults' => $users->getTotalItemCount(),
            'roles' => $this->roleRepository->findAll(),
            'filterService' => $this->filterService,
            'lastPage' => ceil($users->getTotalItemCount() / $users->getItemNumberPerPage())
        ]);
    }

    public function renderNew(): Response
    {
        return $this->render('admin/user/new.html.twig', [
            'roles' => $this->roleRepository->findAll()
        ]);
    }

    public function renderShow(ShowUserRequest $request): Response
    {
        $user = $this->userRepository->find($request->getAttribute('id'));

        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
            'roles' => $this->roleRepository->findAll()
        ]);
    }

    public function processNew(CreateUserRequest $request): Response
    {

        $user = $this->userRepository->createUser(
            $request->getAttribute('password'),
            $request->getAttribute('email'),
            $request->getAttribute('roles'),
            $request->getAttribute('name'),
            $request->getAttribute('surname1'),
            $request->getAttribute('surname2'),
            true,
            new \DateTime(),
        );

        $this->addFlash('success', 'Se ha creado el usuario satisfactoriamente');

        return $this->redirectToRoute('admin_users_render_show', ['id' => $user->getId()]);
    }

    public function processEdit(EditUserRequest $request): Response
    {

        $user = $this->userRepository->find($request->getAttribute('id'));

        $this->userRepository->editUser(
            $user,
            $request->getAttribute('password'),
            $request->getAttribute('email'),
            $request->getAttribute('roles'),
            $request->getAttribute('name'),
            $request->getAttribute('surname1'),
            $request->getAttribute('surname2'),
            $request->getAttribute('enabled'),
            $request->getAttribute('entryDate'),
            $request->getAttribute('leavingDate'),
        );

        $this->addFlash('success', 'Se ha editado el usuario satisfactoriamente');


        return $this->redirectBack();
    }

    public function delete(DeleteUserRequest $request): Response
    {

        $user = $this->userRepository->find($request->getAttribute('id'));

        $this->userRepository->deleteUser(
            $user
        );

        $this->addFlash('success', 'Se ha eliminado el usuario satisfactoriamente');


        return $this->redirectBack();
    }


}
