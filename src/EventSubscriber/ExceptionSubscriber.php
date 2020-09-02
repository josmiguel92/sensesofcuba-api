<?php

namespace App\EventSubscriber;

use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        if(!$event->isMasterRequest() || ($event->getThrowable() instanceof NotFoundHttpException))
        {
            return;
        }
        $message = (new NotificationEmail())->addTo('errors-infonet+josmiguel92@gmail.com')
            ->subject('An internal error occurred at Infonet backend.')
            ->importance(NotificationEmail::IMPORTANCE_HIGH)
            ->content(get_class($event->getThrowable()).'

            '.$event->getThrowable()->getMessage())
            ->attach($event->getThrowable()->getTraceAsString(), 'trace.txt');

        try {
            $this->mailer->send($message);
        } catch (TransportExceptionInterface $e) {
        }

        // ...
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
