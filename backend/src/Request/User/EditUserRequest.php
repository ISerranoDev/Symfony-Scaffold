<?php

namespace App\Request\User;

use App\Entity\User\User;
use App\Request\ValidationRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsNull;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EditUserRequest extends ValidationRequest
{
    ////////////////////////////////////////////////////////////////
    // Scheme used to parse the incoming request to the properties.
    ////////////////////////////////////////////////////////////////

    protected array $schema = [
        'username' => 'username',
        'email' => 'email',
        'password' => 'password',
        're-password' => 'rePassword',
        'roles' => 'roles',
        'enabled' => 'enabled',
        'entry-date' => 'entryDate',
        'leaving-date' => 'leavingDate',
        'name' => 'name',
        'surname1' => 'surname1',
        'surname2' => 'surname2'
    ];

    ////////////////////////////////////////////////////////////////
    // Properties
    ////////////////////////////////////////////////////////////////

    #[NotBlank]
    #[NotNull]
    protected int $id;

    #[NotBlank]
    #[NotNull]
    #[Length(min: 5, max: 30)]
    protected null|string $username = null;

    #[NotBlank]
    #[NotNull]
    #[Email]
    protected null|string $email = null;

    #[Regex(pattern: '/^(?=(?:.*\d))(?=.*[A-Z])(?=.*[a-z])(?=.*[.,*!?¿¡#$%&])\S{8,64}$/', message: 'La contraseña debe tener entre 8 y 64 caracteres: una mayúscula, una minúscula, un número y un carácter especial.')]
    protected ?string $password = null;

    #[Regex(pattern: '/^(?=(?:.*\d))(?=.*[A-Z])(?=.*[a-z])(?=.*[.,*!?¿¡#$%&])\S{8,64}$/', message: 'La contraseña debe tener entre 8 y 64 caracteres: una mayúscula, una minúscula, un número y un carácter especial.')]
    protected ?string $rePassword = null;

    #[NotBlank]
    #[NotNull]
    #[Count(min:1, minMessage: 'Se debe seleccionar al menos un rol.')]
    protected array $roles = [];

    #[Type('boolean', message: 'El valor es incorrecto.')]
    protected null|string|bool $enabled = null;

    #[DateTime('d/m/Y H:i', message: 'La fecha tiene un formato incorrecto.')]
    protected null|string|\DateTime $entryDate;

    #[DateTime('d/m/Y H:i', message: 'La fecha tiene un formato incorrecto.')]
    protected null|string|\DateTime $leavingDate;

    protected null|string $name = null;

    protected null|string $surname1 = null;

    protected null|string $surname2 = null;

    public function __construct(RequestStack $request, ValidatorInterface $validator, EntityManagerInterface $em)
    {
        parent::__construct($request, $validator);

        if($this->entryDate){
            $this->entryDate = \DateTime::createFromFormat('d/m/Y H:i', $this->entryDate);
        }else{
            $this->entryDate = null;
        }

        if($this->leavingDate){
            $this->leavingDate = \DateTime::createFromFormat('d/m/Y H:i', $this->leavingDate);
        }else{
            $this->leavingDate = null;
        }

        if($this->rePassword !== $this->password){
            $this->errors['re-password'][] = 'Las contraseñas introducidas no coinciden.';
        }

        $repository = $em->getRepository(User::class);

        $emailUser = $repository->findOneBy(['email' => $this->email]);
        if($emailUser && $emailUser->getId() != $this->id){
            $this->errors['email'][] = 'Este correo electrónico ya está en uso.';
        }

        $usernameUser = $repository->findOneBy(['username' => $this->username]);
        if($usernameUser && $usernameUser->getId() != $this->id){
            $this->errors['username'][] = 'Este nombre de usuario ya está en uso.';
        }

    }

    public function customProcess(): void
    {

        $this->id = @$this->getRequest()->attributes->get('id');

        $this->enabled = boolval($this->enabled);
    }

}