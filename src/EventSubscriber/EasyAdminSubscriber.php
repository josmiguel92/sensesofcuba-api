<?php


namespace App\EventSubscriber;

use App\Entity\User;
use App\Entity\UserRole;
use App\Message\Events\UserAccountEnabled;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use MsgPhp\Domain\Event\Enable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var MessageBusInterface
     */
    private $bus;

    /**
     * EasyAdminSubscriber constructor.
     * @var EntityManager
     */
    public function __construct(ManagerRegistry $entityManager, MailerInterface $mailer, RouterInterface $router, MessageBusInterface $bus)
    {
//        $this->em = $em->getManager();
        $this->entityManager = $entityManager->getManager();
        $this->mailer = $mailer;
        $this->router = $router;
        $this->bus = $bus;
    }


    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return array(
                'easy_admin.pre_update' => [
                    'sendEnabledAccountNotificationToUser'
                ],

//                'easy_admin.pre_new' => array('setUploadedImagesAsGallery'),
//                'easy_admin.post_new' => array('setUploadedImagesAsGallery'),
//                'easy_admin.pre_persist' => [
//                    'setUpRoles',
////                    'setUploadedImagesInDestinationsAndActivities'
//                    ],
//                'easy_admin.pre_update' => [
//                    'setUpRoles',
////                    'setUploadedImagesInDestinationsAndActivities'
//                    ],
//
//                'easy_admin.post_initialize' => [
//                    'setUploadedImagesInDestinationsAndActivities'
//                    ],
            );
    }

    public function sendEnabledAccountNotificationToUser(GenericEvent $event)
    {
        $user = $event->getArgument('entity');
        if(!($user instanceof  User))
            return;

        if($user->isEnabled() && !$user->getWasEnabled())
        {
            $user->setWasEnabled(true);
            $this->bus->dispatch(new UserAccountEnabled($user->getEmail(), $user->getName()));
        }

        $event->setArgument('entity', $user);
    }

//    public function setUpRoles(GenericEvent $event): void
//    {
//        $entity = $event->getSubject();
//        $request = $event->getArgument('request');
//
//        $TestClass = null;
//        if (is_array($entity) && isset($entity['class'])){
//            $TestClass = $entity['class'];
//            $TestClass =  new $TestClass();
//        }
//        else $TestClass = $entity;
//
//        //If not implements DescriptionFragmentFieldInterface......
//        if(!($TestClass instanceof DescriptionFragmentFieldInterface))
//            return;

//        $relatedEntityId = $request->query->get('id');
//        $destination = $request->get('destination');
//        $activity = $request->get('activity');

        //Get the related data from Request Object, from destination or activity field, conditional(activity or destination)..
//        [$entityName, $currentRepo, $sentData] =  $destination ?
//                                ['destination', $this->destinationRepository, $destination] :
//                                ['activity', $this->activityRepository, $activity];
//
//        if($relatedEntityId)
//        {
//            $entityObj = $currentRepo->find($relatedEntityId);
//            if($entityObj && is_array($sentData['descriptionFragment'])) {
//                foreach ($sentData['descriptionFragment'] as &$item) {
//                    $item[$entityName] = $entityObj;
//                }
//                unset($item);
//            }
//        }

//        $request->request->set($entityName, $sentData);
//        $event['request'] = $request;
//    }

}