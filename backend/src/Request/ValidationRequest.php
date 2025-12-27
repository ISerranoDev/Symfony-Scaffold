<?php

namespace App\Request;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\tokenManager\tokenManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class ValidationRequest
{

    const ERROR_DISPLAY_FORM_FIELD = 1;
    const ERROR_DISPLAY_FLASH_MESSAGE = 2;

    protected array $schema = [];
    protected array $errors = [];
    private ?Request $request;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly ValidatorInterface $validator,
        private readonly CsrfTokenManagerInterface $tokenManager,
    )
    {
        $this->request = $requestStack->getCurrentRequest();

        $this->processRequest($requestStack->getCurrentRequest());
        $this->customProcess();
        $this->validate();
        $this->returnOrContinue();
    }

    public function validate(): void
    {

        if($csrfToken = $this->getCsrfToken()){
            if(!$this->tokenManager->isTokenValid(new CsrfToken($csrfToken, $this->request->get('_csrf_token')))){
                $this->errors['csrf_token'][] = 'Token CSRF inválido.';
                return;
            }
        }

        $properties = get_class_vars($this::class);

        //ignore non field properties
        unset($properties['schema']);
        unset($properties['request']);
        unset($properties['requestStack']);
        unset($properties['validator']);
        unset($properties['tokenManager']);
        unset($properties['errors']);

        foreach ($properties as $propertyName => $val) {
            $propertyField = array_search($propertyName, $this->schema);
            $validation = $this->validator->validatePropertyValue($this::class, $propertyName, $this->$propertyName);
            foreach ($validation as $error) {
                if ($error->getPropertyPath() !== $propertyName) {
                    $this->errors[$propertyField . $error->getPropertyPath()][] = $error->getMessage();
                }else{
                    $this->errors[$propertyField][] = $error->getMessage();
                }
            }
        }
    }

    public function setAttribute(string $name, mixed $value): void
    {
        if(array_key_exists($name, $this->schema)){
            $property = $this->schema[$name];

            $this->$property = $value;
        }
    }

    public function getAttribute(string $property): mixed
    {
        return $this->$property;
    }

    public function processRequest(Request $request): void
    {
        $method = $request->getMethod();
        $params = [];

        if($method == 'GET'){

            $params = [];
            if($this->includeQueryParams()){
                $params = array_merge_recursive($params, $request->query->all());
            }

            try {
                $params = array_merge_recursive($params, @$request->toArray() ?? []);
            }catch (\Exception $e){

            }

        }else if($method == 'POST'){
            $params = $request->request->all();
        }

        if($this->appendFiles()){
            $params = array_replace_recursive($params, $request->files->all());
        }

        foreach ($params as $key => $value) {
            $this->setAttribute($key, $value);
        }

    }

    public function getErrorDisplay() : int
    {
        return self::ERROR_DISPLAY_FORM_FIELD;
    }

    public function customProcess() : void
    {
        // Let this method void in parent class. If you need to do some checking before validate,
        // override this method
    }

    public function getCsrfToken() : ?string
    {
        return null;
    }

    public function returnOrContinue(): void
    {
        if(!$this->isValid()){

            if($this->getErrorDisplay() == self::ERROR_DISPLAY_FORM_FIELD){
                $this->requestStack->getSession()->getFlashBag()->add('error', "El formulario contiene errores.");
                $this->requestStack->getSession()->set('__autocomplete_form', $this->getAutocompleteForm());
                $this->requestStack->getSession()->set('errors', $this->getErrors());
            }else{
                foreach ($this->getErrors() as $fieldErrors){
                    foreach ($fieldErrors as $errorMessage){
                        $this->requestStack->getSession()->getFlashBag()->add('error', $errorMessage);
                    }
                }
            }



            $url = parse_url($this->request->headers->get('referer'))['path'];
            $response = new RedirectResponse($url, 302);
            $response->send();
            exit();

        }
    }

    public function getRequest(): ?Request
    {
        return $this->request;
    }

    public function isValid() : bool
    {
        return !(count($this->errors) > 0);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function appendFiles(): bool
    {
        return false;
    }

    protected function includeQueryParams(): bool
    {
        return false;
    }

    public function getAllAttributes(): array
    {
        $properties = get_class_vars($this::class);

        //ignore non field properties
        unset($properties['schema']);
        unset($properties['request']);
        unset($properties['requestStack']);
        unset($properties['validator']);
        unset($properties['errors']);
        unset($properties['firewall']);
        unset($properties['tokenManager']);


        return $properties;
    }

    public function getRawValues(): array
    {
        $values = [];

        if ($this->getRequest()->getMethod() == 'GET' && $this->includeQueryParams()) {
            $values = $this->getRequest()->query->all();
        } else if ($this->getRequest()->getMethod() == 'POST') {
            $values = $this->getRequest()->request->all();
        }

        return $values;
    }

    public function getAutocompleteForm(null|array $discard = []): array
    {
        $formData = $this->getRawValues();
        unset($formData['_csrf_token']);

        if ($formData && is_array($formData) && sizeof($formData) > 0) {
            foreach ($formData as $name => $value) {
                if (in_array($name, $discard)) {
                    unset($formData[$name]);
                }
            }
        }

        return [
            'form_uri' => $this->request->getRequestUri(),
            'form_method' => $this->request->getMethod(),
            'form_data' => $formData,
        ];
    }
}
