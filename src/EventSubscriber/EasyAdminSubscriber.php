<?php


namespace App\EventSubscriber;

use App\Entity\User;
use App\Entity\UserRole;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * EasyAdminSubscriber constructor.
     * @var EntityManager
     */
    public function __construct(
                                ManagerRegistry $entityManager)
    {
//        $this->em = $em->getManager();
        $this->entityManager = $entityManager->getManager();
    }


    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return array(
//                'easy_admin.pre_new' => array('setUploadedImagesAsGallery'),
//                'easy_admin.post_new' => array('setUploadedImagesAsGallery'),
                'easy_admin.pre_persist' => [
                    'setUpRoles',
//                    'setUploadedImagesInDestinationsAndActivities'
                    ],
                'easy_admin.pre_update' => [
                    'setUpRoles',
//                    'setUploadedImagesInDestinationsAndActivities'
                    ],
//
//                'easy_admin.post_initialize' => [
//                    'setUploadedImagesInDestinationsAndActivities'
//                    ],
            );
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
    public function setUpRoles(GenericEvent $event): void
    {
            $entity = $event->getSubject();

            //Add entities contains gallery
            if (!($entity instanceof User))
            {
                return;
            }



            foreach ($entity->getRoles() as $userRole )
            {
                if($userRole instanceof UserRole){
                      $this->entityManager->persist($userRole);
                }
            }

            $event['entity'] = $entity;

        }
}