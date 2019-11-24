<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use MsgPhp\User\Command\EnableUser;
use MsgPhp\User\Event\UserConfirmed;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class EnableConfirmedUser implements MessageHandlerInterface
{
    private $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function __invoke(UserConfirmed $event): void
    {
        $this->notify($event->user);
    }

    public function notify(User $user): void
    {
        if ($user->isEnabled()) {
            return;
        }

        $this->bus->dispatch(new EnableUser($user->getId()));
    }
}
