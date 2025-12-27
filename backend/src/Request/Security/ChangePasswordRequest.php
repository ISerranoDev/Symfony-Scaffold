<?php

namespace App\Request\Security;

use App\Request\ValidationRequest;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class ChangePasswordRequest extends ValidationRequest
{
    ////////////////////////////////////////////////////////////////
    // Scheme used to parse the incoming request to the properties.
    ////////////////////////////////////////////////////////////////

    protected array $schema = [
        'password' => 'password',
        're-password' => 'rePassword',
    ];

    ////////////////////////////////////////////////////////////////
    // Properties
    ////////////////////////////////////////////////////////////////

    #[NotBlank]
    #[NotNull]
    protected null|string $code = null;

    #[NotBlank]
    #[NotNull]
    #[Email]
    protected null|string $password = null;

    #[NotBlank]
    #[NotNull]
    #[Email]
    protected null|string $rePassword = null;


    public function customProcess(): void
    {
        $this->code = urldecode(urldecode(@$this->getRequest()->attributes->get('code')));

        if($this->password !== $this->rePassword) {
            $this->errors['re-password'] = 'Las contraseñas no coinciden.';
        }
    }

    public function getCsrfToken(): ?string
    {
        return 'change-password';
    }

}
