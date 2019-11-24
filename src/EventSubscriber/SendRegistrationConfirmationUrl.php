<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use MsgPhp\User\Event\UserCreated;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Twig\Environment;
use Symfony\Component\Mailer\MailerInterface;

final class SendRegistrationConfirmationUrl implements MessageHandlerInterface
{
    private $mailer;
    private $twig;

    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function __invoke(UserCreated $event): void
    {
        $this->notify($event->user);
    }

    public function notify(User $user): void
    {
        if ($user->isConfirmed()) {
            return;
        }

        $params = ['user' => $user];
        $message = (new TemplatedEmail())
//        $message = (new \Swift_Message('Confirm your account at The App'))
            ->addTo($user->getEmail())
            ->htmlTemplate('@email/user/confirm_registration.html.twig')
            ->textTemplate('@email/user/confirm_registration.txt.twig')
            ->context($params)
        ;

        $this->mailer->send($message);
    }
}
