<?php

namespace App\Service\Security;


use App\Entity\Role\Role;
use App\Entity\User\User;
use App\Repository\Role\RoleRepository;
use App\Repository\User\UserRepository;
use App\Request\Security\ChangePasswordRequest;
use App\Request\Security\RecoverRequest;
use App\Request\Security\RegisterRequest;
use App\Utils\AbstractClasses\AbstractService;
use App\Utils\Classes\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use ISerranoDev\EncryptBundle\Service\EncryptService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityService extends AbstractService
{


    private AuthenticationUtils $authenticationUtils;
    private EntityRepository | UserRepository $userRepository;
    private EntityRepository | RoleRepository $roleRepository;
    private EncryptService $encryptService;
    private MailerService $mailerService;

    public function __construct(
        AuthenticationUtils $authenticationUtils,
        EntityManagerInterface $entityManager,
        MailerService $mailerService,
        EncryptService $encryptService
    )
    {
        $this->userRepository = $entityManager->getRepository(User::class);
        $this->roleRepository = $entityManager->getRepository(Role::class);

        $this->authenticationUtils = $authenticationUtils;
        $this->mailerService = $mailerService;
        $this->encryptService = $encryptService;
    }

    public function login(): Response
    {

        if ($this->getUser()) {
             return $this->redirectToRoute('app_dashboard');
        }

        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    public function register(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }


        return $this->render('security/register.html.twig', [
        ]);
    }

    public function registerProcess(RegisterRequest $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }


        $password = bin2hex(openssl_random_pseudo_bytes(8));

        $roleUser = $this->roleRepository->findOneBy(['name' => [Role::ROLE_USER]]);

        $this->userRepository->createUser(
            $password,
            $request->getAttribute('email'),
            [$roleUser],
            null,
            null,
            null,
            true,
            new \DateTime()
        );

        $emailCheck = $this->mailerService->sendEmail(
            $request->getAttribute('email'),
            'Registro en la aplicación',
            'email/register.html.twig',
            [
                'user_email' => $request->getAttribute('email'),
                'user_password' => $password
            ]
        );

        if($emailCheck){
            $this->addFlash('success', 'Revise su bandeja de entrada del correo electrónico, le hemos enviado un email con sus datos de acceso.');

        }else{
            $this->addFlash('error', 'No se ha podido enviar su correo electrónico.');

        }

        return $this->redirectToRoute('app_login');
    }

    public function recover(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }


        return $this->render('security/recover.html.twig', [
        ]);
    }

    public function recoverProcess(RecoverRequest $request): RedirectResponse
    {

        if($user = $this->userRepository->findOneBy(['email' => $request->getAttribute('email')])){

            if($user->getRecoverCodeExpiration() && $user->getRecoverCodeExpiration() > (new \DateTime())){
                $this->addFlash('error', 'Ya tiene un enlace de recuperación vigente, consulte su correo electrónico.');
            }else{
                $recoverCode = $this->encryptService->encryptData($user->getEmail());
                $this->userRepository->updateRecovery(
                    $user,
                    $recoverCode,
                    (new \DateTime())->modify('+10 minutes')
                );

                $emailCheck = $this->mailerService->sendEmail(
                    $user->getEmail(),
                    'Recuperación de contraseña',
                    'email/recover.html.twig',
                    [
                        'code' => $recoverCode
                    ]
                );

               if($emailCheck){
                   $this->addFlash('success', 'Se le ha enviado un correo electrónico con un enlace de recuperación. Éste tendrá una validez de 10 minutos.');
               }else{
                   $this->addFlash('error', 'No se ha podido enviar el correo electrónico.');
               }

            }

        }else{
            $this->addFlash('error', 'No existe un usuario con este email');
        }


        return $this->redirectToRoute('app_recover_password_render');

    }

    public function changePasswordRender(): Response
    {

        // need double url encode because of a bug with backslashed encoded one time
        $code = urldecode(urldecode($this->getRequest()->get('code')));

        if($user = $this->userRepository->findOneBy(['email' => $this->encryptService->decryptData($code)])) {
            return $this->render('security/change-password.html.twig', ['code' => $code]);
        }else{
            $this->addFlash('error', 'La ruta a la que intenta acceder no está disponible');
            return $this->redirectToRoute('app_login');
        }

    }

    public function changePasswordProcess(ChangePasswordRequest $request): Response
    {

        if($user = $this->userRepository->findOneBy(['email' => $this->encryptService->decryptData($request->getAttribute('code'))])) {

            if($request->getAttribute('password') == $request->getAttribute('re-password')){

                if(preg_match('/^(?=(?:.*\d))(?=.*[A-Z])(?=.*[a-z])(?=.*[.,*!?¿¡#$%&])\S{8,64}$/', $request->getAttribute('password'))){
                    $this->userRepository->upgradePassword($user, $request->getAttribute('password'), true);
                    $this->addFlash('success', 'La contraseña se ha modificado correctamente.');

                    return $this->redirectToRoute('app_login');
                }else{
                    $this->addFlash('error', 'Las contraseña debe de tener entre 8 y 64 caracteres, un número, un símbolo y una letra mayuscula y una minúscula.');
                }

            }else{
                $this->addFlash('error', 'Las contraseñas no coinciden.');
            }

        }else{
            $this->addFlash('error', 'El usuario que desea modificar no es válido');
        }

        return $this->redirectToRoute('app_change_password_render', ['code' => $this->getRequest()->get('code')]);

    }
}
