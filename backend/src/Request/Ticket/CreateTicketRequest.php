<?php

namespace App\Request\Ticket;

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

class CreateTicketRequest extends ValidationRequest
{
    ////////////////////////////////////////////////////////////////
    // Scheme used to parse the incoming request to the properties.
    ////////////////////////////////////////////////////////////////

    protected array $schema = [
        'title' => 'title',
        'description' => 'description',
    ];

    ////////////////////////////////////////////////////////////////
    // Properties
    ////////////////////////////////////////////////////////////////

    #[NotBlank]
    #[NotNull]
    protected null|string $title = null;

    protected null|string $description = null;

}