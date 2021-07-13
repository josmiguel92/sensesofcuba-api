<?php

namespace App\MessageHandler;

use App\Message\CompleteProductDeletionMessage;
use App\Message\DeleteProductMessage;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CompleteProductDeletionMessageHandler implements MessageHandlerInterface
{
    public function __invoke(CompleteProductDeletionMessage $message)
    {
        // set parent as null for all children.
        // delete them if the deleteChild flag is set to true
        $product = $this->productRepository->find($message->id);
        $this->registry->getManager()->remove($product);
        $this->registry->getManager()->flush();
    }

    /**
     * DeleteProductMessageHandler constructor.
     */
    public function __construct(
        private ProductRepository $productRepository,
        private ManagerRegistry $registry,
    ) {
    }

}
