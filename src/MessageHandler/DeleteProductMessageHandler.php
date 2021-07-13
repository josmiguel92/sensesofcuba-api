<?php

namespace App\MessageHandler;

use App\Message\CompleteProductDeletionMessage;
use App\Message\DeleteProductMessage;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final class DeleteProductMessageHandler implements MessageHandlerInterface
{

    /**
     * DeleteProductMessageHandler constructor.
     */
    public function __construct(
        private ProductRepository $productRepository,
        private ManagerRegistry $registry,
        private MessageBusInterface $bus
    ) {
    }

    public function __invoke(DeleteProductMessage $message)
    {
        // set parent as null for all children.
        // delete them if the deleteChild flag is set to true
        $product = $this->productRepository->find($message->id);
        if ($product !== null) {
            foreach ($product->getProducts() as $child) {
                if ($message->deleteChildren) {
                    $this->bus->dispatch(new DeleteProductMessage($child->getId(), $message->deleteChildren));
                } else {
                    $child->setParent(null);
                    $this->registry->getManager()->persist($child);
                }
            }

            $this->registry->getManager()->flush();

            if ($message->deleteChildren) {
                $this->bus->dispatch(
                    new CompleteProductDeletionMessage($product->getId()),
                    [new DispatchAfterCurrentBusStamp()]);
            } else {
                $this->registry->getManager()->remove($product);
                $this->registry->getManager()->flush();
            }

        }
    }
}
