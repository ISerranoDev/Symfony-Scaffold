<?php

namespace App\Request\Ticket;

use App\Request\ValidationRequest;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

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

    public function getCsrfToken(): ?string
    {
        return 'create-ticket';
    }

}
