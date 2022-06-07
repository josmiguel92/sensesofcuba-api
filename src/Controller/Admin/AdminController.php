<?php

namespace App\Controller\Admin;

use App\Entity\ProductNotification;
use App\Entity\SocProduct;
use App\Entity\User;
use App\Message\Events\NotifyUserAboutProductUpdate;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Messenger\MessageBusInterface;

class AdminController extends EasyAdminController
{
    private MessageBusInterface $bus;
    /**
     * AdminController constructor.
     */
    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }


    /**
     * Send notifications to users from products updates
     *
     * @param array $ids
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws \Throwable
     */
    public function sendBatchAction(array $ids)
    {
        $class = $this->entity['class'];
        if ($class !== 'App\Entity\ProductNotification') {
            return;
        }

        $em = $this->getDoctrine()->getManagerForClass($class);

        foreach ($ids as $id) {
            $notification = $em->find($class, $id);
            if (
                !$notification->isCompleted() && $notification->getTargetUsersCount() > 0
            ) {
                if ($notification->getProduct() instanceof SocProduct) {
                    $productId = $notification->getProduct()->getId();
                } else {
                    $productId = null;
                }

                foreach ($notification->getTargetUsers() as $user) {
                    if ($user instanceof User) {
                        $this->bus->dispatch(
                            new NotifyUserAboutProductUpdate($user, $productId)
                        );
                    }
                }

                $notification->markAsCompleted();
            }
        }

        $this->em->flush();

        // don't return anything or redirect to any URL because it will be ignored
        // when a batch action finishes, user is redirected to the original page
    }
}
