<?php


namespace App\MessageHandler;

use App\Message\Events\PasswordReseted;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class PasswordResetedHandler implements MessageHandlerInterface
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * PasswordRequestHandler constructor.
     * @param MailerInterface $mailer
     */
       public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }


    public function __invoke(PasswordReseted $message)
    {
        $email = (new NotificationEmail())
            ->to($message->getEmail())
            ->subject('Your password at Senses of Cuba was reset')
            ->markdown(<<<EOF
Hi!
Just for inform you... 

**Your account at Senses of Cuba have been modified.**

If it was not you... get in contact!


Have a nice day!
EOF
        )
         ->importance(NotificationEmail::IMPORTANCE_HIGH)
         ;

        $this->mailer->send($email);
    }
}