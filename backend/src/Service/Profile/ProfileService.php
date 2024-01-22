<?php

namespace App\Service\Profile;


use App\Entity\User\User;
use App\Repository\User\UserRepository;
use App\Request\Profile\EditProfileRequest;
use App\Utils\AbstractClasses\AbstractService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ProfileService extends AbstractService
{

    private \Doctrine\ORM\EntityRepository | UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->userRepository = $entityManager->getRepository(User::class);
    }

    public function profile(): Response
    {

        return $this->render('profile/show.html.twig', [
        ]);
    }

    public function renderEdit(): Response
    {
        return $this->render('profile/edit.html.twig', [
        ]);
    }

    public function processEdit(EditProfileRequest $request): Response
    {
        if(!$request->isValid()){
            $this->addFlash('error', 'No se ha podido editar el perfil');
            return $this->redirectToRoute('app_profile_render_edit', ['errors' => $request->getErrors()]);
        }

        if($this->isCsrfTokenValid('edit-profile', $this->getRequest()->request->get('_csrf_token'))){

            $user = $this->getUser();

            $this->userRepository->editProfile(
                $user,
                $request->getAttribute('password'),
                $request->getAttribute('name'),
                $request->getAttribute('surname1'),
                $request->getAttribute('surname2')
            );

            $this->addFlash('success', 'Se ha editado el perfil satisfactoriamente');

        }

        return $this->redirectToRoute('app_profile');
    }

}
