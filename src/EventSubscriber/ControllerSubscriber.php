<?php
/**
 * Created by PhpStorm.
 * User: jo
 * Date: 11/14/2019
 * Time: 11:06 PM
 */

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ControllerSubscriber implements  EventSubscriberInterface{

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => [
                ['setLanguageRequestAsGlobal', 0],

            ],
            KernelEvents::RESPONSE => [
                ['addContentAllowHeader', 0],

            ],
        ];
    }


    /**
     * @param ControllerArgumentsEvent $event
     * Set the current language from the request in controller event as global in the $_SESSION var
     */
    public function setLanguageRequestAsGlobal(ControllerArgumentsEvent $event)
    {
        $parameters = $event->getRequest()->attributes->all();

        if(isset($parameters['_locale']))
            $_SESSION['_locale'] = $parameters['_locale'];
    }

    public function addContentAllowHeader(ResponseEvent $event)
    {
//        $event->getResponse()->headers->set("Access-Control-Allow-Origin", '192.168.43.139');
        $event->getResponse()->headers->set("Access-Control-Allow-Methods", 'POST, GET, OPTIONS');
        //$event->getResponse()->headers->set("Access-Control-Allow-Headers", 'X-PINGOTHER');
    }
}