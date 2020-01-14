<?php


namespace App\MessageHandler;


use App\Message\Events\UserAccountEnabled;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Address;
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
     * UserAccountEnabledHandler constructor.
     */
    public function __construct(MailerInterface $mailer, RouterInterface $router)
    {
        $this->mailer = $mailer;
        $this->router = $router;
    }

    public function __invoke(UserAccountEnabled $message)
    {

        $name = $message->getName();
        $emailAddress = $message->getEmail();
        $email = (new NotificationEmail())
            ->to($emailAddress)
            ->subject('Your account at Senses of Cuba was enabled.')
            ->markdown(<<<EOF
Hi $name!
Your account at senses Of Cuba was enabled.

You can start to explore ours products.

Have a nice day!
EOF
        )
         ->action('Login', $this->router->generate('homepage', [], 0 ))
         ->importance(NotificationEmail::IMPORTANCE_MEDIUM)
         ;

        $this->mailer->send($email);
    }


}