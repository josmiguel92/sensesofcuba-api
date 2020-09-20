<?php


namespace App\MessageHandler;


use App\Message\Events\PasswordReset;
use App\Message\Events\UserRegistered;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
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
     * @param RouterInterface $router
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

        $newUser = $this->userRepository->find($message->getUserId());

        //if no Admin is subscribed to emails or isnt any admin or there in no newUser on DB
        if(!$receiversList || !$newUser) {
            return;
        }

        $email = new TemplatedEmail();

        foreach ($receiversList as $address)
        {
            $email->addBcc((new Address($address)));
        }


         $email
            ->subject('There is a new user registered at Senses of Cuba')
            ->htmlTemplate('email/abacus/cases/new-user.html.twig')
            ->context([
                'subject' => 'New account on Senses of Cuba',
                'username' =>  $newUser->getName(),
                'action_url' => $this->router->generate('easyadmin', [], 0 ) ,
            ])
          ->priority(Email::PRIORITY_HIGH);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return;
        }
        catch (HandlerFailedException  $e) {
            return;
        }

    }
}
