<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Events as AuthenticationEvents;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\AuthenticationEvents as SecurityAuthEvents;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent as SecurityAuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use App\Repository\UserRepository;
use MsgPhp\User\Infrastructure\Security\UserIdentity;


final class OnJWTAuthenticationSuccess implements EventSubscriberInterface
{
    private $JWTTokenManager;
    private $cookieName = '_socAuth';
    private $userRepository;

    /**
     * OnJWTAuthenticationSuccess constructor.
     * @param JWTTokenManagerInterface $JWTTokenManager
     */
    public function __construct(JWTTokenManagerInterface $JWTTokenManager, UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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
            ],
            KernelEvents::REQUEST => [
                ['addRequestAuthorizationIfCookieExist', 250]
            ]
        ];
    }

    public function addRequestAuthorizationIfCookieExist(RequestEvent $event): void
    {
        if(strpos('admin', $event->getRequest()->getPathInfo()))
            dump($event);

        $token = null;
        if(isset($_COOKIE[$this->cookieName]))
        {
            $cookie = json_decode($_COOKIE[$this->cookieName], true);
            $token = $cookie['token'];
        }

        $event->getRequest()->headers->add(
            ['Authorization' => 'Bearer '.$token ]
        );

    }

    /**
     * @param AuthenticationSuccessEvent $event
     * Add to JWT response the user info on AuthenticationSuccessEvent
     */
    public function addUserInfoOnJWTAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();
        $data = $event->getData();

        foreach ($this->getUserDetails($user) as $key => $value){
            $data[$key] = $value;
        }

        setcookie($this->cookieName, json_encode($data), 0 , '/');
        $event->setData($data);

    }

    /**
     * @param SecurityAuthenticationSuccessEvent $event
     */
    public function addUserInfoOnSymfonyAuthenticationSuccess(SecurityAuthenticationSuccessEvent $event): void
    {
        if($event->getAuthenticationToken()->getRoleNames())
        {
            $data = [];
            $user = $event->getAuthenticationToken()->getUser();

            if($user instanceof UserInterface)
            {
                $this->JWTTokenManager->setUserIdentityField('originUsername');

                $data = $this->getUserDetails($user);
                $data['token'] = $this->JWTTokenManager->create($user);
            }

            setcookie($this->cookieName, json_encode($data), 0 , '/');
        }

    }

        /**
     * @param UserInterface $user
     * @return array
     */
    private function getUserDetails(UserInterface $user): array
    {
        $username = $user->getOriginUsername();
        $_user = $this->userRepository->find($user->getUserId());
        if($_user)
        {
            if(!$_user->isEnabled())
                throw new AccessDeniedHttpException('Account disabled');

        $roleAdmin = in_array('ROLE_ADMIN', $user->getRoles(), true) ? 0 : null;
        return [
            'username' => $username,
            'roles' => $roleAdmin,
            'active' => $_user->isEnabled()
            ];
        }

    }

}
