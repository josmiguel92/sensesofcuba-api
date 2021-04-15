<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\User;
use App\Message\Events\UserRegistered;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Mailer\MailerInterface;

final class SendRegistrationConfirmationUrl implements MessageHandlerInterface
{
    private MailerInterface $mailer;
    private RouterInterface $router;
    /**
     * @var MessageBusInterface
     */
    private MessageBusInterface $bus;
    /**
     * @var UserRepository
     */
    private UserRepository $repository;


    public function __construct(
        MailerInterface $mailer,
        RouterInterface $router,
        MessageBusInterface $bus,
        UserRepository $repository
    ) {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->bus = $bus;
        $this->repository = $repository;
    }

    public function __invoke(UserRegistered $event): void
    {
        $user = $this->repository->find($event->getUserId());
        if ($user instanceof User) {
            $this->notify($user);
        }
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
                    ['token' => $user->getConfirmationToken()],
                    0
                ) ,
            ])
          ->priority(Email::PRIORITY_HIGH);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface | HandlerFailedException $e) {
            return;
        }
    }
}
