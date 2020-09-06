<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use App\Message\Events\UserRegistered;
use MsgPhp\User\Event\UserCreated;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Mailer\MailerInterface;
use MsgPhp\User\Infrastructure\Security\UserIdentity;
final class SendRegistrationConfirmationUrl implements MessageHandlerInterface
{
    private $mailer;
    private $router;
    /**
     * @var MessageBusInterface
     */
    private $bus;

    public function __construct(MailerInterface $mailer, RouterInterface $router, MessageBusInterface $bus)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->bus = $bus;
    }

    public function __invoke(UserCreated $event): void
    {
        $this->notify($event->user);
        $this->bus->dispatch(new UserRegistered($event->user->getId()));
    }

    public function notify(User $user): void
    {
        if ($user->isConfirmed()) {
            return;
        }

        $email = (new TemplatedEmail())

            ->to($user->getEmail())
            ->subject('You have created a Senses of Cuba Infonet account')
            ->htmlTemplate('email/abacus/cases/welcome.html.twig')
            ->context([
                'subject' => 'You have created a Senses of Cuba Infonet account',
                'username' =>  $user->getName(),
                'action_url' => $this->router->generate(
                    'confirm_registration',
                    ['token'=> $user->getConfirmationToken()],
                    0) ,
            ])
          ->priority(Email::PRIORITY_HIGH);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return;
        }
    }
}
