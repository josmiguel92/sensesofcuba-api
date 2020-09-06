<?php
/**
 * Created by PhpStorm.
 * User: jo
 * Date: 11/19/2019
 * Time: 5:30 PM
 */
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class MessageSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        // return the subscribed events, their methods and priorities
        return [
            MessageEvent::class => ['addFromToEmails', 0],
        ];
    }

    public function addFromToEmails(MessageEvent $event): void
    {
        if($event->getMessage() instanceof Email)
        {
            $event->getMessage()->from(new Address('no-reply.infonet@sensesofcuba.com', 'SensesOfCuba'));
        }
    }

}
