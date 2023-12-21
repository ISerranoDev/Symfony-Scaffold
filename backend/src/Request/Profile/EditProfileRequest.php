<?php

namespace App\Request\Profile;

use App\Entity\User\User;
use App\Request\ValidationRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EditProfileRequest extends ValidationRequest
{
    ////////////////////////////////////////////////////////////////
    // Scheme used to parse the incoming request to the properties.
    ////////////////////////////////////////////////////////////////

    protected array $schema = [
        'password' => 'password',
        're-password' => 'rePassword',
        'name' => 'name',
        'surname1' => 'surname1',
        'surname2' => 'surname2'
    ];

    ////////////////////////////////////////////////////////////////
    // Properties
    ////////////////////////////////////////////////////////////////

    #[Regex(pattern: '/^(?=(?:.*\d))(?=.*[A-Z])(?=.*[a-z])(?=.*[.,*!?¿¡#$%&])\S{8,64}$/', message: 'La contraseña debe tener entre 8 y 64 caracteres: una mayúscula, una minúscula, un número y un carácter especial.')]
    protected ?string $password = null;

    #[Regex(pattern: '/^(?=(?:.*\d))(?=.*[A-Z])(?=.*[a-z])(?=.*[.,*!?¿¡#$%&])\S{8,64}$/', message: 'La contraseña debe tener entre 8 y 64 caracteres: una mayúscula, una minúscula, un número y un carácter especial.')]
    protected ?string $rePassword  = null;

    protected null|string $name = null;

    protected null|string $surname1 = null;

    protected null|string $surname2 = null;

    public function __construct(RequestStack $request, ValidatorInterface $validator)
    {
        parent::__construct($request, $validator);

        if($this->rePassword !== $this->password){
            $this->errors['re-password'][] = 'Las contraseñas introducidas no coinciden.';
        }

    }


}