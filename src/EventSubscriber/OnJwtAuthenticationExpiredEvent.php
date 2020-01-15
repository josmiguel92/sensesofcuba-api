<?php


namespace App\EventSubscriber;


use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;

class OnJwtAuthenticationExpiredEvent extends AbstractController implements EventSubscriberInterface
{

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            'kernel.response' => [
                'redirectToHomeResponseFromAdmin',
            ]
        ];
    }



    public function redirectToHomeResponseFromAdmin(ResponseEvent $event)
    {
//        header($event->getRequest()->get('_route'));
        $response = $event->getResponse();
        if($response->getStatusCode() === 401 && substr_count($event->getRequest()->getPathInfo(), 'admin/'))
        {
            $event->setResponse($this->redirectToRoute('homepage'));
        }
    }


}