<?php

namespace App\MessageHandler;

use App\Entity\User;
use App\Message\ConfirmUserAccount;
use App\Message\CreateUser;
use App\Message\Events\UserRegistered;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserHandler implements MessageHandlerInterface
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $registry;
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;
    /**
     * @var MessageBusInterface
     */
    private MessageBusInterface $bus;


    /**
     * CreateUserHandler constructor.
     * @param ManagerRegistry $registry
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param MessageBusInterface $bus
     */
    public function __construct(
        ManagerRegistry $registry,
        UserPasswordEncoderInterface $passwordEncoder,
        MessageBusInterface $bus
    )
    {
        $this->registry = $registry;
        $this->passwordEncoder = $passwordEncoder;
        $this->bus = $bus;
    }

    public function __invoke(CreateUser $message)
    {
        // Encode the plain password, and set it.
        $data = $message->getUserRaw();
        $user = new User(
            $data['email'],
            '',
            $data['name'],
            $data['enterprise'],
            $data['travelAgency'],
            $data['country'],
            $data['web']
        );
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $data['password']);

        $user->setPassword($encodedPassword);
        $this->registry->getManager()->persist($user);
        $this->registry->getManager()->flush();

        $this->bus->dispatch(new UserRegistered($user->getId()));
    }
}
