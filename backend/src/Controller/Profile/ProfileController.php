<?php

namespace App\Controller\Profile;

use App\Request\Profile\EditProfileRequest;
use App\Service\Profile\ProfileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/mi-perfil')]
class ProfileController extends AbstractController
{

    public function __construct(
        private readonly ProfileService $profileService
    )
    {
    }

    #[Route(path: '/', name: 'app_profile')]
    public function profile(): Response
    {
        return $this->profileService->profile();
    }

    #[Route(path: '/editar', name: 'app_profile_process_edit', methods: 'POST')]
    public function processEdit(EditProfileRequest $request): Response
    {
        return $this->profileService->processEdit($request);
    }


}
