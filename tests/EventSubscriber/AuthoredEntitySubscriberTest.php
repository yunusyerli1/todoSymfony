<?php

namespace App\Tests\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\EventSubscriber\AuthoredEntitySubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
class AuthoredEntitySubscriberTest extends TestCase
{
    public  function testConfiguration()
    {
        $result = AuthoredEntitySubscriber::getSubscribedEvents();
        $this->assertArrayHasKey(KernelEvents::VIEW, $result);
        $this->assertEquals(
            ['getAuthenticatedUser', EventPriorities::PRE_WRITE],
            $result[KernelEvents::VIEW]);
    }

//    public  function  testSetAuthorCall()
//    {
//        $tokenStorageMock = $this->getTokenStorageMock();
//
//        $eventMock = $this->getMockBuilder(GetResponseForControllerResultEvent::class)
//            ->disableOriginalConstructor()
//            ->getMock();
//
//        (new AuthoredEntitySubscriber($tokenStorageMock))->getAuthenticatedUser(
//            $eventMock
//        );
//    }
//
//    /**
//     * @return MockObject|TokenStorageInterface
//     */
//    private function getTokenStorageMock(bool $hasToken = true): MockObject
//    {
//        $tokenMock = $this->getMockBuilder(TokenInterface::class)
//            ->getMockForAbstractClass();
//        $tokenMock->expects($hasToken ? $this->once() : $this->never())
//            ->method('getUser')
//            ->willReturn(new User());
//
//        $tokenStorageMock = $this->getMockBuilder(TokenStorageInterface::class)
//            ->getMockForAbstractClass();
//        $tokenStorageMock->expects($this->once())
//            ->method('getToken')
//            ->willReturn($hasToken ? $tokenMock : null);
//
//        return $tokenStorageMock;
//    }

}
