<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Events as AuthenticationEvents;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\AuthenticationEvents as SecurityAuthEvents;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent as SecurityAuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;


final class OnJWTAuthenticationSuccess implements EventSubscriberInterface
{
    private $JWTTokenManager;

    /**
     * OnJWTAuthenticationSuccess constructor.
     * @param JWTTokenManagerInterface $JWTTokenManager
     */
    public function __construct(JWTTokenManagerInterface $JWTTokenManager)
    {
        $this->JWTTokenManager = $JWTTokenManager;
    }

    public static function getSubscribedEvents(): array
    {
        // return the subscribed events, their methods and priorities
        return [
            AuthenticationEvents::AUTHENTICATION_SUCCESS => [
                ['addUserInfoOnJWTAuthenticationSuccess', 0],
            ],
            SecurityAuthEvents::AUTHENTICATION_SUCCESS => [
                ['addUserInfoOnSymfonyAuthenticationSuccess', 0]
            ]
        ];
    }



    /**
     * @param AuthenticationSuccessEvent $event
     * Add to JWT response the user info on AuthenticationSuccessEvent
     */
    public function addUserInfoOnJWTAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();
        $data = $event->getData();

        [$data['username'], $data['roles']] = $this->getUserDetails($user);
//        $data['username']  = $user->getOriginUsername();
//        $data['roles']  = array_values($user->getRoles());

        $event->setData($data);

        return;
    }

    private function getUserDetails(UserInterface $user): array
    {
        return [$user->getOriginUsername(),  array_values($user->getRoles())];
    }

    public function addUserInfoOnSymfonyAuthenticationSuccess(SecurityAuthenticationSuccessEvent $event): void
    {
        if($event->getAuthenticationToken()->getRoleNames())
        {
            $data = [];

            $user = $event->getAuthenticationToken()->getUser();
            if($user instanceof UserInterface)
            {
                [$data['username'], $data['roles']] = $this->getUserDetails($user);
                $data['token'] = $this->JWTTokenManager->create($user);
            }


            dump($data);
        }

    }
}
