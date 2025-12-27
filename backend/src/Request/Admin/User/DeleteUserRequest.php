<?php

namespace App\Request\Admin\User;


use App\Entity\User\User;
use App\Request\ValidationRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DeleteUserRequest extends ValidationRequest
{
    ////////////////////////////////////////////////////////////////
    // Scheme used to parse the incoming request to the properties.
    ////////////////////////////////////////////////////////////////



    ////////////////////////////////////////////////////////////////
    // Properties
    ////////////////////////////////////////////////////////////////

    #[NotBlank]
    #[NotNull]
    protected string $id;

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
        $this->id = @$this->getRequest()->attributes->get('id');
    }

    public function validate(): void
    {
        parent::validate();

        $exist = $this->em->getRepository(User::class)->find($this->id);
        if (!$exist) {
            $this->errors['id'][] = 'El usuario que intenta eliminar no existe.';
        }

    }

    public function getCsrfToken(): ?string
    {
        return 'delete-user';
    }

}
