<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Events as AuthenticationEvents;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;


final class OnJWTAuthenticationSuccess implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        // return the subscribed events, their methods and priorities
        return [
            AuthenticationEvents::AUTHENTICATION_SUCCESS => [
                ['addUserInfoOnAuthenticationSuccess', 0],
            ],
        ];
    }


    /**
     * @param AuthenticationSuccessEvent $event
     * Add to JWT response the user info on AuthenticationSuccessEvent
     */
    public function addUserInfoOnAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();
        $data = $event->getData();

        $data['username']  = $user->getOriginUsername();
        $data['roles']  = array_values($user->getRoles());

        $event->setData($data);

        return;
    }
}
