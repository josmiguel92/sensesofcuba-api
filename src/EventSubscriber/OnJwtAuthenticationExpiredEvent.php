<?php

namespace App\EventSubscriber;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class OnJwtAuthenticationExpiredEvent extends AbstractController implements EventSubscriberInterface
{

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.response' => [
                'redirectToHomeResponseFromAdmin',
            ]
        ];
    }

    public function redirectToHomeResponseFromAdmin(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        if($response->getStatusCode() === 401 && self::isAdminRoute($event->getRequest()->getPathInfo()))
        {
            //clear cookie
            OnJWTAuthenticationSuccess::unsetCookie();
            $event->setResponse($this->redirectToRoute('homepage'));
        }
    }

    private static function isAdminRoute($route): bool
    {
        return (bool)substr_count($route, 'admin');
    }

}
