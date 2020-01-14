<?php


namespace App\MessageHandler;


use App\Message\Events\PasswordReseted;
use App\Message\Events\UserRegistered;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

class UserRegisteredHandler implements MessageHandlerInterface
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
     * @var Router
     */
    private $router;

    /**
     * PasswordRequestHandler constructor.
     * @param MailerInterface $mailer
     * @param UserRepository $userRepository
     */
       public function __construct(MailerInterface $mailer, UserRepository $userRepository, RouterInterface $router)
    {
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
        $this->router = $router;
    }


    public function __invoke(UserRegistered $message)
    {
        $notifyUser = $this->userRepository->findBy(['receiveEmails'=>true]);
        $receiversList = [];
        if($notifyUser)
        {
            foreach ($notifyUser as $user)
            {
//                $name = $user->getName();
                $email = $user->getEmail();
                $receiversList[] = $email;
            }
        }

        if(!$receiversList)
            return;

        $email = (new NotificationEmail());

        foreach ($receiversList as $address)
        {
            $email->addBcc((new Address($address)));
        }

        $newUser = $this->userRepository->find($message->getUserId());
        $newUserName = $newUser->getName();
        $email->subject('A new user created an account at Senses of Cuba and await for approbation.')
            ->markdown(<<<EOF
Hi!
There is a new user ($newUserName) at Senses of Cuba intranet... 

**The new account is not enabled until you (or another Admin) approve it.**

You can go to Administration page and review the new accounts profile. 

Have a nice day!
EOF
        )
         ->action('Go to Admin', $this->router->generate('easyadmin', [], 0 ))
         ->importance(NotificationEmail::IMPORTANCE_HIGH)
         ;

        $this->mailer->send($email);
    }
}