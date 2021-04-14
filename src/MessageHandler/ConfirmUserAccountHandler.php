<?php


namespace App\MessageHandler;

use App\Entity\User;
use App\Message\ConfirmUserAccount;
use App\Message\Events\UserAccountConfirmed;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;

class ConfirmUserAccountHandler implements MessageHandlerInterface
{

    /**
     * @var MessageBusInterface
     */
    private MessageBusInterface $bus;
    /**
     * @var UserRepository
     */
    private UserRepository $repository;
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $registry;

    public function __construct(MessageBusInterface $bus, UserRepository $repository, ManagerRegistry $registry)
    {
        $this->bus = $bus;
        $this->repository = $repository;
        $this->registry = $registry;
    }

    public function __invoke(ConfirmUserAccount $event): void
    {
        $user = $this->repository->find($event->userId);

        if ($user instanceof User) {
            $user->confirm();

            $this->registry->getManager()->persist($user);
            $this->registry->getManager()->flush();
        }
    }
}
