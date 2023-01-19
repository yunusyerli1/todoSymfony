<?php

use ApiPlatform\Symfony\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\PublishedDateEntityInterface;

class PublishedDateEntitySubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        // TODO: Implement getSubscribedEvents() method.
        return [
            KernelEvents::VIEW => ['setDatePublished', EventPriorities::PRE_WRITE]
        ];
    }

    public function setDatePublished(ViewEvent $event)
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if ((!$entity instanceof PublishedDateEntityInterface ) || Request::METHOD_POST !== $method) {
            return;
        }
        $entity->setPublished(new \DateTime());
    }
}
