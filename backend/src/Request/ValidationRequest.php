<?php

namespace App\Request;

use ReflectionProperty;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class ValidationRequest
{

    protected array $schema = [];
    protected array $errors = [];
    protected bool $appendFiles = false;

    protected ValidatorInterface $validator;
    private ?Request $request;

    public function __construct(RequestStack $request, ValidatorInterface $validator)
    {
        $this->request = $request->getCurrentRequest();
        $this->validator = $validator;

        $this->processRequest($request->getCurrentRequest());
        $this->customProcess();
        $this->validate();
    }

    public function validate(): void
    {
        $properties = get_class_vars($this::class);

        //ignore non field properties
        unset($properties['schema']);
        unset($properties['request']);
        unset($properties['validator']);
        unset($properties['errors']);
        unset($properties['appendFiles']);

        foreach ($properties as $propertyName => $val) {
            $propertyField = array_search($propertyName, $this->schema);
            $validation = $this->validator->validatePropertyValue($this::class, $propertyName, $this->$propertyName);
            foreach ($validation as $error) {
                $this->errors[$propertyField][] = $error->getMessage();
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
            $params = $request->toArray();
        }else if($method == 'POST'){
            $params = $request->request->all();
        }

        if($this->appendFiles){
            $params = array_merge($params, $request->files->all());
        }

        foreach ($params as $key => $value){
            $this->setAttribute($key, $value);
        }

    }

    public function customProcess() : void
    {
        // Let this method void in parent class. If you need to do some checking before validate,
        // override this method
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

}