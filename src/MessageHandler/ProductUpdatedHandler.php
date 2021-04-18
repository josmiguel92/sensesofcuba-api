<?php

namespace App\MessageHandler;

use App\Entity\ProductNotification;
use App\Message\Events\ProductUpdated;
use App\Repository\SocProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ProductUpdatedHandler implements MessageHandlerInterface
{
    /**
     * @var SocProductRepository
     */
    private SocProductRepository $productRepository;
    /**
     * @var MessageBusInterface
     */
    private MessageBusInterface $bus;
    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * PasswordRequestHandler constructor.
     * @param SocProductRepository $productRepository
     * @param MessageBusInterface $bus
     * @param EntityManagerInterface $manager
     */
    public function __construct(
        SocProductRepository $productRepository,
        MessageBusInterface $bus,
        EntityManagerInterface $manager
    ) {
        $this->productRepository = $productRepository;
        $this->bus = $bus;
        $this->manager = $manager;
    }


    public function __invoke(ProductUpdated $message)
    {
        if (!$product = $this->productRepository->find($message->getProductId())) {
            return;
        }

        $product->updateTimestamps();
        try {
            $this->manager->persist($product);
        } catch (ORMException $e) {
        }

        if ($product->getParent() != null) {
            $this->bus->dispatch(
                new ProductUpdated(
                    $product->getParent()->getId(),
                    'Child changed/added: ' . $product->getReferenceName(),
                    $message->getSendNotification()
                )
            );
        }

        if ($message->getSendNotification()) {
            $subscribedUsers = $product->getSubscribedUsers();

            $notification = new ProductNotification($product, $message->getChangesStr(), $subscribedUsers->getValues());
            try {
                $this->manager->persist($notification);
            } catch (ORMException $e) {
            }
        }

        try {
            $this->manager->flush();
        } catch (OptimisticLockException | ORMException $e) {
        }
    }
}
