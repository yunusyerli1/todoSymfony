<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Exception\EmptyBodyException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class EmptyBodySubscriber implements EventSubscriberInterface
{
    const ERROR_EMPTY_BODY = "The body of the POST/PUT method cannot be empty";
    const ERROR_EMPTY_BODY_CODE = 400;
    private $logger;
    public function __construct( LoggerInterface $logger){
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST  => ['handleEmptyBody', EventPriorities::POST_DESERIALIZE,],
        ];
    }

    public function handleEmptyBody(RequestEvent $event, )
    {
        $request = $event->getRequest();
        $method = $request->getMethod();
        $route = $request->get('_route');

        $this->logger->info('Handle empty body request' . $request);
        $this->logger->info('Handle empty body method' . $method);
        $this->logger->info('Handle empty body route' . $route);
        $this->logger->info('Handle empty body contenttype' . $request->getContentTypeFormat());

        if (!in_array($method, [Request::METHOD_POST, Request::METHOD_PUT]) ||
            in_array($request->getContentTypeFormat(), ['html', 'form']) ||
            substr($route, 0, 3) !== 'api') {
            return;
        }

        $data = $event->getRequesSt()->get('data');

        if (null === $data) {
            throw new EmptyBodyException();
        }

        /*$data = $event->getRequesSt()->get('data');

        if (null === $data) {
            throw new EmptyBodyException();
        }*/
    }
}
