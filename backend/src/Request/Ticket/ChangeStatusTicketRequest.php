<?php

namespace App\Request\Ticket;

use App\Request\ValidationRequest;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;

class ChangeStatusTicketRequest extends ValidationRequest
{
    ////////////////////////////////////////////////////////////////
    // Scheme used to parse the incoming request to the properties.
    ////////////////////////////////////////////////////////////////

    protected array $schema = [
        'closed' => 'closed'
    ];

    ////////////////////////////////////////////////////////////////
    // Properties
    ////////////////////////////////////////////////////////////////

    #[NotBlank]
    #[NotNull]
    protected null|string $id = null;

    #[Type('boolean', message: 'El valor es incorrecto.')]
    protected null|string|bool $closed = null;

    public function customProcess(): void
    {
        $this->id = @$this->getRequest()->attributes->get('id');
        $this->closed = boolval($this->closed);
    }

    public function getCsrfToken(): ?string
    {
        return 'change-status-ticket';
    }

}
