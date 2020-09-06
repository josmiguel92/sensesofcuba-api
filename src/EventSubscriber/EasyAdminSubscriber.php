<?php


namespace App\EventSubscriber;

use App\Entity\User;
use App\Message\Events\UserAccountEnabled;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\MessageBusInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    /**
     * @var MessageBusInterface
     */
    private $bus;

    /**
     * EasyAdminSubscriber constructor.
     * @var EntityManager
     */
    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }


    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return ['easy_admin.pre_update' => 'sendEnabledAccountNotificationToUser'];
    }

    public function sendEnabledAccountNotificationToUser(GenericEvent $event): void
    {
        $user = $event->getArgument('entity');
        if(!($user instanceof  User)) {
            return;
        }

        if($user->isEnabled() && !$user->getWasEnabled())
        {
            $user->setWasEnabled(true);
            $this->bus->dispatch(new UserAccountEnabled($user->getEmail(), $user->getName()));
        }

        $event->setArgument('entity', $user);
    }

}
