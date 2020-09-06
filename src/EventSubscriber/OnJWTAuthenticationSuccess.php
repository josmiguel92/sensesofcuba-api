<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Events as AuthenticationEvents;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use App\Repository\UserRepository;
use MsgPhp\User\Infrastructure\Security\UserIdentity;


final class OnJWTAuthenticationSuccess implements EventSubscriberInterface
{
    /**
     * @var string
     */
    public static $cookieName = '_socAuth';

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * OnJWTAuthenticationSuccess constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public static function getSubscribedEvents(): array
    {
        // return the subscribed events, their methods and priorities
        return [
            AuthenticationEvents::AUTHENTICATION_SUCCESS => [
                ['addUserInfoOnJWTAuthenticationSuccess', 250],
            ],
            KernelEvents::REQUEST => [
                ['addRequestAuthorizationIfCookieExist', 250]
            ]
        ];
    }

    public function addRequestAuthorizationIfCookieExist(RequestEvent $event): void
    {

        $token = null;

        if(($authorization = $event->getRequest()->headers->get('Authorization')) && str_contains($authorization, 'Bearer '))
        {
            return;
        }

        if($event->getRequest()->cookies->get(self::$cookieName))
        {
            try {
                $cookie = json_decode($_COOKIE[self::$cookieName], true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $cookie = ['token' => null];
            }
            $token = $cookie['token'];
        }

        $event->getRequest()->headers->add(
            ['Authorization' => 'Bearer '.$token ]
        );

    }

    /**
     * @param AuthenticationSuccessEvent $event
     * Add to JWT response the user info on AuthenticationSuccessEvent
     * @throws \JsonException
     */
    public function addUserInfoOnJWTAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();
        $data = $event->getData();

        foreach ($this->getUserDetails($user) as $key => $value){
            $data[$key] = $value;
        }

        $cookieExpire = time() + (int)(60 * 60 * 1.5);
        $cookie = Cookie::create(
            self::$cookieName,
            json_encode($data, JSON_THROW_ON_ERROR),
            $cookieExpire,
            null,
            null,
            null,
            true,
            false,
            Cookie::SAMESITE_STRICT
        );

        $event->getResponse()->headers->setCookie($cookie);

        $event->setData($data);

    }


        /**
     * @param UserInterface $user
     * @return array
     */
    private function getUserDetails(UserInterface $user): array
    {
        if($user instanceof UserIdentity) {
            $username = $user->getOriginUsername();

            $_user = $this->userRepository->find($user->getUserId());
            if($_user)
            {
                if(!$_user->isEnabled()) {
                    throw new AccessDeniedHttpException('Account disabled');
                }

            $roleAdmin = ('ROLE_ADMIN' === $_user->getRole() OR 'ROLE_EDITOR' === $_user->getRole()) ? 0 : -1;
            return [
                'username' => $username,
                'roles' => $roleAdmin,
                'active' => $_user->isEnabled()
                ];
            }
        }
        return [];
    }

    public static function unsetCookie(): void
    {
        setcookie(
            self::$cookieName,
            "",
            [
                'expires'  => time() - 3600 * 3600,
                'path'     => '/',
                'samesite' => 'Strict'
            ]
        );
    }




}
