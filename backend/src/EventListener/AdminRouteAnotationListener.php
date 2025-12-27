<?php

namespace App\EventListener;

use App\Attribute\AdminRoute;
use App\Entity\User\User;
use ReflectionException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Bundle\SecurityBundle\Security;

#[AsEventListener(KernelEvents::CONTROLLER)]
class AdminRouteAnotationListener implements EventSubscriberInterface
{

    /**
     * @var Security
     */
    private Security $security;
    private RequestStack $requestStack;

    public function __construct(Security $security, RequestStack $requestStack)
    {
        $this->security = $security;
        $this->requestStack = $requestStack;
    }

    /**
     * @param ControllerEvent $event
     * @throws ReflectionException
     */
    public function onKernelController(ControllerEvent $event): void
    {
        if (!is_array($controllers = $event->getController())) {
            return ;
        }

        list($controller, $methodName) = $controllers;

        // Check if the controller has the AdminRoute annotation
        $reflectionClass = new \ReflectionClass($controller);
        $classAnnotation = $reflectionClass->getAttributes(AdminRoute::class);

        // Check if the method of the controller has the AdminRoute annotation
        $reflectionObject = new \ReflectionObject($controller);
        $reflectionMethod = $reflectionObject->getMethod($methodName);

        /** @var AdminRoute $methodAnnotation */
        $methodAnnotation = $reflectionMethod->getAttributes(AdminRoute::class);

        if (!($classAnnotation || $methodAnnotation)) {
            return;
        }

        $this->handleAnnotationAction($event->getRequest());

    }


    private function handleAnnotationAction(Request $request): void
    {
        // If the logged user has the attribute created in Cert Login, he can continue, else, trigger an error
        if(
            !$this->security->getUser() ||
            (
                $this->security->getUser() instanceof User &&
                !$this->security->getUser()->isAdmin()
            )

        )
        {
            $this->requestStack->getSession()->getFlashBag()->add('error', "No tienes permiso para acceder a este recurso");


            $url = parse_url($request->headers->get('referer'))['path'];
            $response = new RedirectResponse($url, 302);
            $response->send();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => [
                ['onKernelController', 10]
            ],
        ];
    }
}