<?php

namespace Asmodine\FrontBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ExceptionListener.
 */
class ExceptionListener
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RouterInterface
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $code = $event->getException()->getCode();

        if (!in_array($code, [Response::HTTP_NOT_FOUND, Response::HTTP_FORBIDDEN, Response::HTTP_INTERNAL_SERVER_ERROR])) {
            return;
        }

        $status = Response::HTTP_FOUND;
        if (in_array($code, [Response::HTTP_NOT_FOUND])) {
            $status = Response::HTTP_MOVED_PERMANENTLY;
        }

        $response = new RedirectResponse($this->router->generate('asmodinefront_main_home'), $status);

        $event->setResponse($response);
    }
}
