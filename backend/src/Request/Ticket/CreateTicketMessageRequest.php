<?php

namespace App\Request\Ticket;

use App\Request\ValidationRequest;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class CreateTicketMessageRequest extends ValidationRequest
{
    ////////////////////////////////////////////////////////////////
    // Scheme used to parse the incoming request to the properties.
    ////////////////////////////////////////////////////////////////

    protected array $schema = [
        'message' => 'message'
    ];

    ////////////////////////////////////////////////////////////////
    // Properties
    ////////////////////////////////////////////////////////////////

    #[NotBlank]
    #[NotNull]
    protected null|string $id = null;

    #[NotBlank]
    #[NotNull]
    protected null|string $message = null;

    public function customProcess(): void
    {
        $this->id = @$this->getRequest()->attributes->get('id');
    }

    public function getCsrfToken(): ?string
    {
        return 'create-ticket-message';
    }

}
