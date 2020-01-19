<?php


namespace App\MessageHandler;

use App\Message\Events\PasswordReset;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class PasswordResetHandler implements MessageHandlerInterface
{
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * PasswordRequestHandler constructor.
     * @param MailerInterface $mailer
     * @param UserRepository $userRepository
     */
       public function __construct(MailerInterface $mailer, UserRepository $userRepository)
    {
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
    }


    public function __invoke(PasswordReset $message)
    {
        $user = $this->userRepository->findOneBy(['email' => $message->getEmail()]);
        if (!$user)
            return;

        $email = (new TemplatedEmail())
            ->to($user->getEmail())
            ->subject('Your password at Senses of Cuba was reset')
            ->htmlTemplate('email/foundation/cases/password-reset.html.twig')
            ->context([
                'subject' => 'Your password at Senses of Cuba was reset',
                'username' => $user->getName(),
            ])
            ->priority(Email::PRIORITY_HIGH);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return;
        }
    }
}