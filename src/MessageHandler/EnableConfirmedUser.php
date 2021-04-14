<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\User;
use App\Message\Events\UserConfirmed;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class EnableConfirmedUser implements MessageHandlerInterface
{
    private MessageBusInterface $bus;
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $registry;

    public function __construct(MessageBusInterface $bus, UserRepository $userRepository, ManagerRegistry $registry)
    {
        $this->bus = $bus;
        $this->userRepository = $userRepository;
        $this->registry = $registry;
    }

    public function __invoke(UserConfirmed $event): void
    {
        $this->notify($event->getUserId());
    }

    public function notify(int $userId): void
    {
        $user = $this->userRepository->find($userId);
        if ($user instanceof User && $user->isEnabled()) {
            return;
        }

        $user->enable();
        $this->registry->getManager()->persist($user);
        $this->registry->getManager()->flush();
    }
}
