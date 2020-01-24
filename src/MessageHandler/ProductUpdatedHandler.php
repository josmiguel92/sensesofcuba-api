<?php


namespace App\MessageHandler;


use App\Message\Events\NotifyUserAboutProductUpdate;
use App\Message\Events\PasswordReset;
use App\Message\Events\ProductUpdated;
use App\Repository\SocProductRepository;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ProductUpdatedHandler implements MessageHandlerInterface
{
    /**
     * @var SocProductRepository
     */
    private $productRepository;
    /**
     * @var MessageBusInterface
     */
    private $bus;

    /**
     * PasswordRequestHandler constructor.
     * @param MailerInterface $mailer
     * @param UserRepository $userRepository
     * @param SocProductRepository $productRepository
     * @param MessageBusInterface $bus
     */
       public function __construct(MailerInterface $mailer, UserRepository $userRepository,
                                   SocProductRepository $productRepository, MessageBusInterface $bus)
    {
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
        $this->bus = $bus;
    }


    public function __invoke(ProductUpdated $message)
    {
        if(!$product = $this->productRepository->find($message->getProductId())) {
            return;
        }

        $subscribedUsers = $product->getSubscribedUsers();

        foreach ($subscribedUsers as $user)
        {
            $this->bus->dispatch(new NotifyUserAboutProductUpdate($user->getId(), $product->getId()));
        }
    }

}