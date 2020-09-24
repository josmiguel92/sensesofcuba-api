<?php

namespace App\Controller;

use App\Entity\ProductNotification;
use App\Message\Events\NotifyUserAboutProductUpdate;
use App\Repository\ProductNotificationRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

class AdminController extends EasyAdminController
{
    public function sendBatchAction(array $ids)
    {
        $class = $this->entity['class'];
        if ($class !== 'App\Entity\ProductNotification') {
            return;
        }

        $bus = $this->get('message_bus');

        $em = $this->getDoctrine()->getManagerForClass($class);

        foreach ($ids as $id) {
            $notification = $em->find($class, $id);
            if (
                $notification instanceof ProductNotification
                && !$notification->isCompleted() && $notification->getTargetUsersCount() > 0
            ) {
                foreach ($notification->getTargetUsers() as $user) {
                    $bus->dispatch(
                        new NotifyUserAboutProductUpdate($user->getId(), $notification->getProduct()->getId())
                    );
                }

                $notification->markAsCompleted();
            }
        }

        $this->em->flush();

        // don't return anything or redirect to any URL because it will be ignored
        // when a batch action finishes, user is redirected to the original page
    }
}
