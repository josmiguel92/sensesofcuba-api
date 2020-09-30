<?php

namespace App\Controller;

use App\Entity\ProductNotification;
use App\Entity\SocProduct;
use App\Entity\User;
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
                if ($notification->getProduct() instanceof SocProduct) {
                    $productId = $notification->getProduct()->getId();
                } else {
                    $productId = null;
                }

                foreach ($notification->getTargetUsers() as $user) {
                    if ($user instanceof User) {
                        $bus->dispatch(
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
