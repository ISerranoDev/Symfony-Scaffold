<?php

namespace App\Request\Security;

use App\Entity\User\User;
use App\Request\ValidationRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;
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


}