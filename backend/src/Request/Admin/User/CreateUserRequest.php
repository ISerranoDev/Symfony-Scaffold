<?php

namespace App\Request\Admin\User;

use App\Entity\Role\Role;
use App\Entity\User\User;
use App\Request\ValidationRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUserRequest extends ValidationRequest
{
    ////////////////////////////////////////////////////////////////
    // Scheme used to parse the incoming request to the properties.
    ////////////////////////////////////////////////////////////////

    protected array $schema = [
        'email' => 'email',
        'password' => 'password',
        're-password' => 'rePassword',
        'roles' => 'roles',
        'name' => 'name',
        'surname1' => 'surname1',
        'surname2' => 'surname2',
    ];

    ////////////////////////////////////////////////////////////////
    // Properties
    ////////////////////////////////////////////////////////////////

    #[NotBlank]
    #[NotNull]
    #[Email]
    protected null|string $email = null;

    #[NotBlank]
    #[NotNull]
    #[Regex(pattern: '/^(?=(?:.*\d))(?=.*[A-Z])(?=.*[a-z])(?=.*[.,*!?¿¡#$%&])\S{8,64}$/', message: 'La contraseña debe tener entre 8 y 64 caracteres: una mayúscula, una minúscula, un número y un carácter especial.')]
    protected null|string $password = null;

    #[NotBlank]
    #[NotNull]
    #[Regex(pattern: '/^(?=(?:.*\d))(?=.*[A-Z])(?=.*[a-z])(?=.*[.,*!?¿¡#$%&])\S{8,64}$/', message: 'La contraseña debe tener entre 8 y 64 caracteres: una mayúscula, una minúscula, un número y un carácter especial.')]
    protected null|string $rePassword = null;

    #[NotBlank]
    #[NotNull]
    #[Count(min:1, minMessage: 'Se debe seleccionar al menos un rol.')]
    protected array $roles = [];

    protected null|string $name = null;

    protected null|string $surname1 = null;

    protected null|string $surname2 = null;

    public function __construct(
        RequestStack $request,
        ValidatorInterface $validator,
        CsrfTokenManagerInterface $tokenManager,
        private readonly EntityManagerInterface $em,
    )
    {
        parent::__construct($request, $validator, $tokenManager);
    }

    public function customProcess(): void
    {
        if($this->rePassword !== $this->password){
            $this->errors['re-password'][] = 'Las contraseñas introducidas no coinciden.';
        }

        $this->roles = $this->roles ? $this->em->getRepository(Role::class)->findByIds($this->roles) : [];
        $repository = $this->em->getRepository(User::class);

        if($repository->findOneBy(['email' => $this->email])){
            $this->errors['email'][] = 'Este correo electrónico ya está en uso.';
        }

    }

    public function getCsrfToken(): ?string
    {
        return 'create-user';
    }

}
