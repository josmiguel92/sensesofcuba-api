<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;

        $this->logger = $logger;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if(!$event->isMasterRequest() || ($event->getThrowable() instanceof NotFoundHttpException))
        {
            return;
        }
        $message = (new NotificationEmail())->addTo('errors-infonet+josmiguel92@gmail.com')
            ->subject('An internal error occurred at Infonet backend.')
            ->importance(NotificationEmail::IMPORTANCE_HIGH)
            ->content(get_class($event->getThrowable())."\n\n".$event->getThrowable()->getMessage())
            ->attach($event->getThrowable()->getTraceAsString(), 'trace.txt');

        try {
            $this->mailer->send($message);
        } catch (TransportExceptionInterface $e) {
            $this->logger->emergency('Cannot connect to email server or error sending the error notification to admin');
        }
        catch (HandlerFailedException $e){
            $this->logger->emergency('Cannot connect to email server or error sending the error notification to admin');
        }
    }

    public static function getSubscribedEvents(): array
    {
        return ['kernel.exception' => 'onKernelException'];
    }
}
