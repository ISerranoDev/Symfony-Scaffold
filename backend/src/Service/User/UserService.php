<?php

namespace App\Service\User;


use App\Entity\Role\Role;
use App\Entity\User\User;
use App\Repository\Role\RoleRepository;
use App\Repository\User\UserRepository;
use App\Request\User\CreateUserRequest;
use App\Request\User\EditUserRequest;
use App\Utils\AbstractClasses\AbstractService;
use App\Utils\Classes\FilterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class UserService extends AbstractService
{

    private \Doctrine\ORM\EntityRepository | UserRepository $userRepository;
    private \Doctrine\ORM\EntityRepository | RoleRepository $roleRepository;
    private FilterService $filterService;

    public function __construct(
        EntityManagerInterface $entityManager,
        FilterService $filterService
    )
    {
        $this->userRepository = $entityManager->getRepository(User::class);
        $this->roleRepository = $entityManager->getRepository(Role::class);

        $this->filterService = $filterService;

    }

    public function list(): Response
    {
        $users = $this->userRepository->list($this->filterService);

        return $this->render('user/index.html.twig', [
            'users' => $users,
            'totalUsers' => $users->getTotalItemCount(),
            'roles' => $this->roleRepository->findAll(),
            'filterService' => $this->filterService
        ]);
    }

    public function renderNew(): Response
    {
        return $this->render('user/new.html.twig', [
            'roles' => $this->roleRepository->findAll()
        ]);
    }

    public function renderShow(int $userId): Response
    {
        $user = $this->userRepository->find($userId);

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'roles' => $this->roleRepository->findAll()
        ]);
    }

    public function renderEdit(int $userId): Response
    {
        $user = $this->userRepository->find($userId);

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'roles' => $this->roleRepository->findAll()
        ]);
    }

    public function processNew(CreateUserRequest $request): Response
    {
        if(!$request->isValid()){
            $this->addFlash('error', 'No se ha podido crear el usuario');
            return $this->redirectToRoute('app_users_render_new', ['errors' => $request->getErrors()]);
        }

        if($this->isCsrfTokenValid('create-user', $this->getRequest()->request->get('_csrf_token'))){

            $roles = $this->roleRepository->findByIds($request->getAttribute('roles'));

            $this->userRepository->createUser(
                $request->getAttribute('username'),
                $request->getAttribute('password'),
                $request->getAttribute('email'),
                $roles,
                $request->getAttribute('name'),
                $request->getAttribute('surname1'),
                $request->getAttribute('surname2'),
                true,
                new \DateTime()
            );

            $this->addFlash('success', 'Se ha creado el usuario satisfactoriamente');

        }else{
            $this->addFlash('error', 'El csrf token no es válido');
        }

        return $this->redirectToRoute('app_users_list');
    }

    public function processEdit(int $userId, EditUserRequest $request): Response
    {
        if(!$request->isValid()){
            $this->addFlash('error', 'No se ha podido editar el usuario');
            return $this->redirectToRoute('app_users_render_edit', ['id' => $userId, 'errors' => $request->getErrors()]);
        }

        if($this->isCsrfTokenValid('edit-user', $this->getRequest()->request->get('_csrf_token'))){

            $user = $this->userRepository->find($userId);

            $roles = $this->roleRepository->findByIds($request->getAttribute('roles'));

            $this->userRepository->editUser(
                $user,
                $request->getAttribute('username'),
                $request->getAttribute('password'),
                $request->getAttribute('email'),
                $roles,
                $request->getAttribute('name'),
                $request->getAttribute('surname1'),
                $request->getAttribute('surname2'),
                $request->getAttribute('enabled'),
                $request->getAttribute('entryDate'),
                $request->getAttribute('leavingDate')
            );

            $this->addFlash('success', 'Se ha editado el usuario satisfactoriamente');

        }else{
            $this->addFlash('error', 'El csrf token no es válido');
        }

        return $this->redirectToRoute('app_users_list');
    }

    public function delete(int $userId): Response
    {
        if($this->isCsrfTokenValid('delete-user', $this->getRequest()->request->get('_csrf_token'))){

            $user = $this->userRepository->find($userId);

            $this->userRepository->deleteUser(
                $user
            );

            $this->addFlash('success', 'Se ha eliminado el usuario satisfactoriamente');

        }else{
            $this->addFlash('error', 'El csrf token no es válido');
        }

        return $this->redirectToRoute('app_users_list');
    }


}
