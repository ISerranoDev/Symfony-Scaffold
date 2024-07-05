<?php

namespace App\Controller\Security;


use App\Request\Security\RegisterRequest;
use App\Service\Security\SecurityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{

    private SecurityService $securityService;

    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    #[Route(path: '/iniciar-sesion', name: 'app_login')]
    public function login(): Response
    {
        return $this->securityService->login();
    }

    #[Route(path: '/registro', name: 'app_register_render', methods: 'GET')]
    public function register(): Response
    {
        return $this->securityService->register();
    }

    #[Route(path: '/registro', name: 'app_register_process', methods: 'POST')]
    public function registerProcess(RegisterRequest $request): Response
    {
        return $this->securityService->registerProcess($request);
    }

    #[Route(path: '/recuperar-contrasena', name: 'app_recover_password_render', methods: 'GET')]
    public function recover(): Response
    {
        return $this->securityService->recover();
    }

    #[Route(path: '/recuperar-contrasena', name: 'app_recover_password_process', methods: 'POST')]
    public function recoverProcess(): Response
    {
        return $this->securityService->recoverProcess();
    }

    #[Route(path: '/cambiar-contrasena/{code}', name: 'app_change_password_render', methods: 'GET')]
    public function changePasswordRender(): Response
    {
        return $this->securityService->changePasswordRender();
    }

    #[Route(path: '/cambiar-contrasena/{code}', name: 'app_change_password_process', methods: 'POST')]
    public function changePasswordProcess(): Response
    {
        return $this->securityService->changePasswordProcess();
    }

    #[Route(path: '/cerrar-sesion', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
