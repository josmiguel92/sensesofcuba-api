<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\User;
use App\Message\PasswordRequest;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Email;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use Twig\Environment;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Routing\RouterInterface;

final class SendPasswordResetUrl implements MessageHandlerInterface
{
    private MailerInterface $mailer;
    private RouterInterface $router;
    /**
     * @var UserRepository
     */
    private UserRepository $repository;

    public function __construct(MailerInterface $mailer, RouterInterface $router, UserRepository $repository)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->repository = $repository;
    }

    public function __invoke(PasswordRequest $event): void
    {
        $user = $this->repository->find($event->getUserId());
        $this->notify($user, $event->token);
    }

    public function notify(User $user, ResetPasswordToken $token): void
    {
        $subject = 'Password recovery requested via SensesOfCuba';
        $email = (new TemplatedEmail())
            ->to($user->getEmail())
            ->subject($subject)
            ->htmlTemplate('email/abacus/cases/password.html.twig')
            ->context([
                'subject' => $subject,
                'username' => $user->getName(),
                'resetToken' => $token
            ])
          ->priority(Email::PRIORITY_HIGH);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface | HandlerFailedException $e) {
            return;
        }
    }
}
