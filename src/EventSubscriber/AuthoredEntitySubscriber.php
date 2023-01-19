<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\AuthoredEntityInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class AuthoredEntitySubscriber implements EventSubscriberInterface
{
    private $security;
    public function __construct( Security $security){
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        // TODO: Implement getSubscribedEvents() method.
        return [
            KernelEvents::VIEW => ['getAuthenticatedUser', EventPriorities::PRE_WRITE]
        ];
    }

    public function getAuthenticatedUser(ViewEvent $event)
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        /**
         * @var UserInterface $author
         */
        $author = $this->security->getUser();

        if((!$entity instanceof AuthoredEntityInterface) || Request::METHOD_POST !== $method) {
            return;
        }
        $entity->setAuthor($author);
    }
}
