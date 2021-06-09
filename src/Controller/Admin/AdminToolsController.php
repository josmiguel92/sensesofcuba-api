<?php

namespace App\Controller\Admin;

use App\Entity\SocProduct;
use App\Entity\TranslatedDocument;
use App\Entity\User;
use App\Entity\UsersGroup;
use App\Message\CreateUser;
use App\Repository\SocImageRepository;
use App\Repository\SocProductRepository;
use App\Repository\UsersGroupRepository;
use Exception;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface as Messenger;
use Symfony\Component\Routing\Annotation\Route;
use App\DataBase\Backup\BackupMySQL;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
            'export' => [
                [
                'name' => 'Export Users data as Excel',
                'link' => $this->generateUrl('admin_tools_export_users_as_xls'),
                'color' => 'primary',
                'icon' => 'fa fa-users'
                ],
                [
                    'name' => 'Export Mysql DataBase as ZIP file (Advanced)',
                    'link' => $this->generateUrl('admin_tools_export_db_download'),
                    'color' => 'secondary',
                    'icon' => 'fa fa-database'
                ],
            ],
            'tools' => [

                [
                    'name' => 'Remake image thumbnails for products images',
                    'link' => $this->generateUrl('admin_tools_remake_thumbs'),
                    'color' => 'secondary',
                    'icon' => 'fa fa-image'
                ],

                [
                    'name' => 'Register fake user and send email to admins',
                    'link' => $this->generateUrl('admin_tools_register_fake_user'),
                    'icon' => 'fa fa-user-alt'
                ],

                [
                    'name' => 'Create default user group',
                    'link' => $this->generateUrl('admin_tools_create_default_user_group'),
                    'icon' => 'fa fa-user-alt'
                ],

                [
                    'name' => 'Merge products child documents',
                    'link' => $this->generateUrl('admin_tools_merge_products_child_documents'),
                    'icon' => 'fa fa-invoice'
                ],

                [
                    'name' => 'Complete english title for products using reference name as public title',
                    'link' => $this->generateUrl('admin_tools_complete_english_title'),
                    'icon' => 'fa fa-invoice'
                ],

            ]
        ]);
    }

    /**
     * @Route("/register_fake_user", name="register_fake_user")
     * @param Messenger $messenger
     * @return RedirectResponse
     */
    public function registerFakeUser(Messenger $messenger)
    {
        $email = uniqid('fakeuser-', false) . '@' . 'sensesofcuba.com';

        $user['email'] = $email;
        $user['password'] = $email;
        $user['name'] = $email;
        $user['enterprise']  = 'SensesOfCuba';
        $user['travelAgency'] = 'Touroperator';
        $user['country'] = 'Cuba';
        $user['web'] = '';

        $messenger->dispatch(new CreateUser($user));

        $this->addFlash('notice', "The user $email was created");

        return $this->redirectToRoute('admin_tools_index');
    }

    /**
     * @Route("/thumbs", name="remake_thumbs")
     * @param SocImageRepository $repository
     * @return RedirectResponse
     */
    public function remakeThumbs(SocImageRepository $repository)
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
        $users = $repository->findAll();

        $userPropertyMap = [
            'name',
            'enterprise',
            'travelAgency',
            'country',
            'web',
            'email',
            'enabled',
            'createdAt',
            'lastLogin',
            'subscribedProducts',
            'hiddenProducts'
        ];

        $dataStr = implode("\t", $userPropertyMap) . "\n";

        foreach ($users as $user) {
            /**
             * @var User $user
             */
            $createdAt = $user->getCreatedAt() ? $user->getCreatedAt()->format('Y-m-d') : null;
            $lastLogin = $user->getLastLogin() ? $user->getLastLogin()->format('Y-m-d H:i') : null;
            $enabled = $user->isEnabled() ? 'Yes' : 'No';
            $row = [
                $user->getName(),
                $user->getEnterprise(),
                $user->getTravelAgency(),
                $user->getCountry(),
                $user->getWeb(),
                $user->getEmail(),
                $enabled,
                $createdAt,
                $lastLogin,
                $user->getSubscribedProductsListAsString(),
                $user->getHiddenProductsListAsString()
            ];
            $dataStr .= implode("\t", $row) . "\n";
        }

        $timeStamp = date("d.m.Y-H.i");
        $filename = "SoC_infonet_users_export_" . $timeStamp . '.csv';

        return new Response($dataStr, Response::HTTP_OK, [
            'Content-Type' => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
            "Content-Encoding" => 'UTF-8'

        ]);
    }


    /**
     * @Route("/export_db_download", name="export_db_download")
     * @param ParameterBagInterface $parameterBag
     * @param Filesystem $filesystem
     * @return Response
     * @throws Exception
     */
    public function exportDBDownload(ParameterBagInterface $parameterBag, Filesystem $filesystem): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        if (!isset($_ENV) || !isset($_ENV['DATABASE_URL'])) {
            throw new ParameterNotFoundException('DATABASE_URL');
        }

        $databaseStr = $_ENV['DATABASE_URL'];
        $dbConfig = parse_url($databaseStr);

        $dbConfig['database'] = isset($dbConfig['path']) ? ltrim($dbConfig['path'], '/') : null;
        $dbConfig['password'] = isset($dbConfig['pass']) ? ltrim($dbConfig['pass'], '/') : null;

        $tables = ["*"];
        $show = [
            'TABLES',
            'DATA'
        ];

        $backup = new BackupMySQL($dbConfig, $tables, $show);

        $relativePath = str_replace('//', DIRECTORY_SEPARATOR, '..//backups//');
        $backupFolder = $_SERVER['DOCUMENT_ROOT'] . $relativePath;

        if (!is_dir($backupFolder)) {
            $filesystem->mkdir($backupFolder);
        }

        if (!is_writable($backupFolder)) {
            throw new IOException(
                sprintf('Unable to write to the "%s" directory.', $backupFolder),
                0,
                null,
                $backupFolder
            );
        }
        $backup->setFolder($backupFolder);

        $backup->zip();
        $backup->download(false);

        return new Response();
    }

    /**
     * @Route("/create_default_user_group", name="create_default_user_group")
     * @param UsersGroupRepository $repository
     * @param ManagerRegistry $registry
     * @return RedirectResponse
     */
    public function createDefaultUserGroup(UsersGroupRepository $repository, ManagerRegistry $registry): RedirectResponse
    {
        foreach (UsersGroup::DEFAULT_GROUPS as $group => $description) {
            $defaultGroup = new UsersGroup($group, $description);
            $registry->getManager()->persist($defaultGroup);
        }

        try {
            $registry->getManager()->flush();
        } catch (Exception $exception) {
            $this->addFlash('error', "Some error ocurred. Maybe the groups already exists.");
        }

        $this->addFlash('notice', "The default users group was created");

        return $this->redirectToRoute('admin_tools_index');
    }

    /**
     * @Route("/admin_tools_complete_english_title", name="complete_english_title")
     * @param SocProductRepository $repository
     * @param ManagerRegistry $registry
     * @return Response
     */
    public function completeEnglishTitle(SocProductRepository $repository)
    {
        //show products that have only one children.
        $allProducts = $repository->findAll();

        $suggestion = [];
        foreach ($allProducts as $product) {
            if (
                $product->getTranslations()->get('en') === null &&
                $product->isEnglishGlobalTranslation()
// &&
                //$product->getTranslations()->get('de') !== null
            ) {

                $product->translate('en')->setName($product->getReferenceName());
                $product->mergeNewTranslations();
                $this->getDoctrine()->getManager()->persist($product);
            }
        }
        $this->getDoctrine()->getManager()->flush();


        return $this->render('admin/tools/merge_products_child_documents.html.twig', [
            'products' => $suggestion
        ]);
    }

    /**
     * @Route("/merge_products_child_documents", name="merge_products_child_documents")
     * @param SocProductRepository $repository
     * @param ManagerRegistry $registry
     * @return Response
     */
    public function mergeProductsChildDocuments(SocProductRepository $repository, ManagerRegistry $registry)
    {
        //show products that have only one children.
        $allProducts = $repository->findAll();

        $suggestion = [];
        foreach ($allProducts as $product) {
            if ($product->getParent() != null

                && count($product->getSocProducts()) == 0 //has no childs
                && count($product->getParent()->getSocProducts()) == 1 //has no siblings

            ) {
                $suggestion[] = $product;
            }
        }


        return $this->render('admin/tools/merge_products_child_documents.html.twig', [
            'products' => $suggestion
        ]);
    }


    /**
     * @Route("/merge_product_as_child_document/{id}", name="merge_product_as_child_document")
     * @param SocProductRepository $repository
     * @param ManagerRegistry $registry
     * @return Response
     */
    public function mergeProductAsChildDocument(
        SocProduct $product,
        SocProductRepository $repository,
        ManagerRegistry $registry
    ) {
        SocProductsTreeController::migrateTranslatedDocumentIntoDocumentCollection($product, $this);

        $parent = $product->getParent();

        SocProductsTreeController::migrateTranslatedDocumentIntoDocumentCollection($parent, $this);
        $documents = [];

        if ($product->getTranslatedDocument() !== null && $product->getTranslatedDocument()->isEmpty() === false) {
            $documents[] = $product->getTranslatedDocument();
        }


        if ($product->getDocumentCollection() !== null && $product->getDocumentCollection()->isEmpty() === false) {
            foreach ($product->getDocumentCollection() as $doc) {
                $documents[] = $doc;
            }
        }


        if (count($documents) == 0) {
            $this->addFlash('notice', 'The product "'.$product->getReferenceName().'" do not contain documents for merge');
        }
        if (count($documents) == 1) {
            $doc = $documents[0];
            if ($doc instanceof TranslatedDocument) {
                $doc->setTitle($product->getTranslatedNameOrReference('en'));
                if ($doc->getEnFile()) {
                    $doc->getEnFile()->setTitle($product->getTranslatedNameOrReference('en'));
                }

                if ($doc->getDeFile()) {
                    $doc->getDeFile()->setTitle($product->getTranslatedNameOrReference('de'));
                }
                $parent->addDocumentCollection($doc);
                $doc->setSocProduct($parent);

                $registry->getManager()->persist($doc);

                $parent->removeSocProduct($product);


                $product->setTranslatedDocument(null);
                $registry->getManager()->remove($product);

                $registry->getManager()->persist($parent);
                $registry->getManager()->flush();



                $this->addFlash('notice', 'The product "'.$product->getReferenceName().'" was merged into its parent: '.$parent->getReferenceName());
            }
        }

        if (count($documents) > 1) {
            $this->addFlash('notice', 'The product "'.$product->getReferenceName().'" contains more than 1 docs for merge. Aborting');
        }


        SocProductsTreeController::cleanEmptyDocumentFromCollection($product, $this);

        return $this->redirectToRoute('admin_tools_merge_products_child_documents');
    }
}
