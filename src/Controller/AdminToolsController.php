<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\SocProduct;
use App\Entity\User;
use App\Form\DocumentType;
use App\Form\SocProductType;
use App\Form\User\RegisterType;
use App\Message\Events\NotifyUserAboutProductUpdate;
use App\Message\Events\ProductUpdated;
use App\Repository\DocumentRepository;
use App\Repository\SocImageRepository;
use App\Repository\SocProductRepository;
use MsgPhp\User\Command\CreateUser;
use MsgPhp\User\Repository\UserRepository;
use MsgPhp\UserBundle\MsgPhpUserBundle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\SerializerStamp;
use Symfony\Component\Mime\MimeTypeGuesserInterface;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Routing\Annotation\Route;

/**
* @Route("/admin/tools", name="admin_tools_", host="%app.main_domain%")
*/
class AdminToolsController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('admin/tools/index.html.twig', [
            'tools' => [
                [
                    'name' => 'Export Users data as Excel',
                    'link' => $this->generateUrl('admin_tools_export_users_as_xls'),
                    'color' => 'primary',
                    'icon' => 'fa fa-users'
                ],
                [
                    'name' => 'Send test emails to admin',
                    'link' => $this->generateUrl('admin_tools_send_test_email'),
                    'color' => 'secondary',
                    'icon' => 'fa fa-send-o'
                ],

//                [
//                    'name' => 'Register fake user',
//                    'link' => $this->generateUrl('admin_tools_register_fake_user')
//                ],

            ]
        ]);
    }

    /**
     * @Route("/send_test_email", name="send_test_email")
     */
    public function send_test_email(MessageBusInterface $bus, SocProductRepository $productRepository, UserRepository $userRepository)
    {
        $currentUser = $this->getUser();
        $email = $currentUser->getUsername();

        try {
            $product = $productRepository->findAll()[0];
            $bus->dispatch(
                (new Envelope(new ProductUpdated($product->getId())))->with(new SerializerStamp([
                // groups are applied to the whole message, so make sure
                // to define the group for every embedded object
                'groups' => ['my_serialization_groups'],
                ]))
            );

            $user = $userRepository->findByUsername($email);
            if ($user instanceof MsgPhpUserBundle\User) {
                $bus->dispatch(new NotifyUserAboutProductUpdate($user->getId(), $product->getId()));
            }
        } catch (\MsgPhp\Domain\Exception\EntityNotFound $e) {
            $this->addFlash('warning', $e->getMessage());
        }

        return $this->redirectToRoute('admin_tools_index');
    }

    /**
     * @Route("/register_fake_user", name="register_fake_user")
     */
    public function register_fake_user(MessageBusInterface $bus)
    {
        $email = uniqid('fakeuser-', false) . '@' . 'sensesofcuba.com';
        $userRaw = [
            'email' => $email,
            'password' => $email,
            'name' => $email,
            'enterprise' => 'SensesOfCuba',
            'country' => 'Cuba',
            'web' => '',
            'travelAgency' => 'Touroperator'
        ];

        $bus->dispatch(new CreateUser($userRaw));

        $this->addFlash('notice', "The user $email was created");

        return $this->redirectToRoute('admin_tools_index');
    }


    /**
     * @Route("/thumbs", name="remake_thumbs")
     */
    public function remake_thumbs(SocImageRepository $repository)
    {
        $images = $repository->findAll();

        foreach ($images as $image) {
            $image->createCustomThumbnail();
        }

        $this->addFlash('notice', "The thumbs was generated");

        return $this->redirectToRoute('admin_tools_index');
    }


    /**
     * @Route("/export_users_as_xls", name="export_users_as_xls")
     * @param \App\Repository\UserRepository $repository
     * @return Response
     */
    public function exportUsersAsXls(\App\Repository\UserRepository $repository)
    {
        $usersArray = $repository->findAllAsArray(
            [
                'name',
                'enterprise',
                'travelAgency',
                'country',
                'web',
                'email',
                'enabled',
                'createdAt'
            ]
        );

        $objToStr = function ($obj) {
            if ($obj instanceof \DateTime) {
                return $obj->format("Y-m-d H:i:s");
            }
            if (is_string($obj)) {
                return $obj;
            }
            return  null;
        };

        $timeStamp = time();
        $filename = "SoC_infonet_users_export_" . $timeStamp . '.csv';

        $isPrintHeader = false;

        $dumpData = "";
        foreach ($usersArray as $row) {
            $row = array_map($objToStr, $row);
            if (!$isPrintHeader) {
                $dumpData .= implode("\t", array_keys($row)) . "\n";
                $isPrintHeader = true;
            }
            $dumpData .= implode("\t", array_values($row)) . "\n";
        }

        return new Response($dumpData, Response::HTTP_OK, [
            'Content-Type' => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
            "Content-Encoding" => 'UTF-8'

        ]);
    }
}
