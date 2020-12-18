<?php

namespace App\EventSubscriber;

use App\Entity\Document;
use App\Entity\News;
use App\Entity\SocProduct;
use App\Entity\User;
use App\Message\Events\UserAccountEnabled;
use Doctrine\ORM\EntityManager;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
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
        return [
            'easy_admin.pre_update' => [
                    ['sendEnabledAccountNotificationToUser', 0],
                    ['updateTimeStamp', 0]
                ]

            ];
    }

    public function sendEnabledAccountNotificationToUser(GenericEvent $event): void
    {
        $object = $event->getArgument('entity');

        if (($object instanceof  User)) {
            $user = $object;
            if ($user->isEnabled() && !$user->getWasEnabled()) {
                $user->setWasEnabled(true);
                $this->bus->dispatch(new UserAccountEnabled($user->getEmail(), $user->getName()));
            }

            $event->setArgument('entity', $user);
        }

    }

    public function updateTimeStamp(GenericEvent $event)
    {
        $object = $event->getArgument('entity');
        if (($object instanceof  News) || ($object instanceof  Document) || ($object instanceof  SocProduct)) {
            $object->updateTimestamps();

            $event->setArgument('entity', $object);
        }
    }
}
