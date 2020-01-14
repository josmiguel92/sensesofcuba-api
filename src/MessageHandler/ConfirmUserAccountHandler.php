<?php


namespace App\MessageHandler;


use App\Message\ConfirmUserAccount;
use App\Message\Events\UserAccountConfirmed;
use MsgPhp\Domain\DomainMessageBus;
use MsgPhp\Domain\Event\Confirm;
use MsgPhp\User\Command\ConfirmUser;
use MsgPhp\User\Event\UserConfirmed;
use MsgPhp\User\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;

class ConfirmUserAccountHandler implements MessageHandlerInterface
{

    /**
     * @var DomainMessageBus
     */
    private $bus;
    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(MessageBusInterface $bus, UserRepository $repository)
    {
        $this->bus = $bus;
        $this->repository = $repository;
    }

    public function __invoke(ConfirmUserAccount $command): void
    {
        $user = $this->repository->find($command->userId);

        /** @var \App\Entity\User $user */
        if($user)
        {
            $user->confirm();
        }
        $this->repository->save($user);

    }
}