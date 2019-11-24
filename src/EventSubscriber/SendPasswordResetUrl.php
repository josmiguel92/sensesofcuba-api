<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use MsgPhp\User\Event\UserPasswordRequested;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Twig\Environment;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Routing\RouterInterface;

final class SendPasswordResetUrl implements MessageHandlerInterface
{
    private $mailer;
    private $twig;
    private $router;

    public function __construct(MailerInterface $mailer, Environment $twig, RouterInterface $router)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->router = $router;
    }

    public function __invoke(UserPasswordRequested $event): void
    {
        $this->notify($event->user);
    }

    public function notify(User $user): void
    {
        if (null === $user->getPasswordResetToken()) {
            return;
        }

        $email = (new NotificationEmail())
            ->to($user->getEmail())
            ->subject('PasswordRequested via SensesOfCuba')
            ->markdown(<<<EOF
Recently you have requested a **new password**.
To create a new one, just click the btn.

EOF
    )
    ->action('Set a new Password', $this->router->generate('reset_password', ['token'=> $user->getPasswordResetToken()], 0) )
    ->importance(NotificationEmail::IMPORTANCE_MEDIUM)
;

        $this->mailer->send($email);
    }
}
