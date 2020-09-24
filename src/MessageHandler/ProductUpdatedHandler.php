<?php

namespace App\MessageHandler;

use App\Entity\ProductNotification;
use App\Message\Events\NotifyUserAboutProductUpdate;
use App\Message\Events\PasswordReset;
use App\Message\Events\ProductUpdated;
use App\Repository\SocProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
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
     * @var EntityManager
     */
    private $manager;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * PasswordRequestHandler constructor.
     * @param MailerInterface $mailer
     * @param UserRepository $userRepository
     * @param SocProductRepository $productRepository
     * @param MessageBusInterface $bus
     * @param EntityManagerInterface $manager
     */
    public function __construct(
        MailerInterface $mailer,
        UserRepository $userRepository,
        SocProductRepository $productRepository,
        MessageBusInterface $bus,
        EntityManagerInterface $manager
    ) {
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
        $this->bus = $bus;
        $this->manager = $manager;
    }


    public function __invoke(ProductUpdated $message)
    {
        if (!$product = $this->productRepository->find($message->getProductId())) {
            return;
        }

        $subscribedUsers = $product->getSubscribedUsers();

        $notification = new ProductNotification($product, $message->getChangesStr(), $subscribedUsers->getValues());
        $this->manager->persist($notification);
        $this->manager->flush();
    }
}
