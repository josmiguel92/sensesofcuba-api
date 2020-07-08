<?php


namespace App\MessageHandler;


use App\Message\Events\UserAccountEnabled;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\RouterInterface;

class UserAccountEnabledHandler implements MessageHandlerInterface
{
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var UserRepository
     */
    private $userRepository;


    /**
     * UserAccountEnabledHandler constructor.
     * @param MailerInterface $mailer
     * @param RouterInterface $router
     * @param UserRepository $userRepository
     */
    public function __construct(MailerInterface $mailer, RouterInterface $router, UserRepository $userRepository)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->userRepository = $userRepository;
    }

    public function __invoke(UserAccountEnabled $message)
    {

        $user = $this->userRepository->findOneBy(['email' => $message->getEmail()]);
        if (!$user) {
            return;
        }

        $email = (new TemplatedEmail())
            ->to($user->getEmail())
            ->subject('Your Senses of Cuba Infonet account has been approved')
            ->htmlTemplate('email/foundation/cases/account-enabled.html.twig')
            ->context([
                'subject' => 'Your account at Senses of Cuba was enabled',
                'username' => $user->getName(),
                'action_url' => $this->router->generate('homepage', [], 0)
            ])
            ->priority(Email::PRIORITY_HIGH);

        //Add Bcc to admins
        $notifyUser = $this->userRepository->findBy(['receiveEmails'=>true]);
        if($notifyUser)
        {
            foreach ($notifyUser as $user)
            {
                $email->addBcc((new Address($user->getEmail())));
            }
        }

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return;
        }

    }


}
