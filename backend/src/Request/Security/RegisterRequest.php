<?php

namespace App\Request\Security;

use App\Entity\User\User;
use App\Request\ValidationRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterRequest extends ValidationRequest
{
    ////////////////////////////////////////////////////////////////
    // Scheme used to parse the incoming request to the properties.
    ////////////////////////////////////////////////////////////////

    protected array $schema = [
        'email' => 'email',
    ];

    ////////////////////////////////////////////////////////////////
    // Properties
    ////////////////////////////////////////////////////////////////

    #[NotBlank]
    #[NotNull]
    #[Email]
    protected null|string $email = null;

    public function __construct(
        RequestStack $request,
        ValidatorInterface $validator,
        CsrfTokenManagerInterface $tokenManager,
        private readonly EntityManagerInterface $entityManager
    )
    {
        parent::__construct($request, $validator, $tokenManager);
    }

    public function customProcess(): void
    {
        $repository = $this->entityManager->getRepository(User::class);

        if ($this->email) {
            $existingUser = $repository->findOneBy(['email' => $this->email]);
            if ($existingUser) {
                $this->errors['email'][] = 'Ya existe un usuario registrado con este correo electrónico.';
            }
        }
    }

    public function getErrorDisplay() : int
    {
        return self::ERROR_DISPLAY_FLASH_MESSAGE;
    }

    public function getCsrfToken(): ?string
    {
        return 'register';
    }
}
