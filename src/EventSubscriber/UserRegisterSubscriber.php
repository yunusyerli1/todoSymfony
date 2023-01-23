<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Email\Mailer;
use App\Entity\User;
use App\Security\TokenGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserRegisterSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected UserPasswordHasherInterface $userPasswordHasher,
        protected TokenGenerator $tokenGenerator,
        protected Mailer $mailer
    ){}

    public static function getSubscribedEvents(): array
    {
        // TODO: Implement getSubscribedEvents() method.
        return [
            KernelEvents::VIEW => ['userRegistered', EventPriorities::PRE_WRITE]
        ];
    }

    public function userRegistered(ViewEvent $event)
    {
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if(!$user instanceof User ||
            !in_array($method, [Request::METHOD_POST])) {
            return;
        }

        //It is an User, we need to hash password here
        $hashedPassword = $this->userPasswordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);

        //Create confirmation token
        $user->setConfirmationToken(
            $this->tokenGenerator->getRandomSecureToken()
        );

        //Send email here...
        $this->mailer->sendConfirmationEmail($user);

    }
}
