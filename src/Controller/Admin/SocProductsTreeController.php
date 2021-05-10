<?php

namespace App\Controller\Admin;

use A2lix\AutoFormBundle\Tests\Fixtures\Entity\Product;
use App\Entity\SocProduct;
use App\Entity\TranslatedDocument;
use App\Entity\UsersGroup;
use App\Form\SocProductType;
use App\Message\Events\ProductUpdated;
use App\Repository\SocProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
* @Route("/admin/product", name="soc_product_", host="%app.main_domain%")
*/
class SocProductsTreeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param SocProductRepository $productRepository
     * @return Response
     */
    public function index(SocProductRepository $productRepository)
    {
        return $this->render('soc_products_tree/index.html.twig', [
            'products' => $productRepository->findBy(['parent' => null])
        ]);
    }

    /**
     * @Route("/new", name="new", methods="GET|POST")
     * @param Request $request
     * @param MessageBusInterface $bus
     * @return RedirectResponse|Response
     */
    public function new(Request $request, MessageBusInterface $bus)
    {
        $product = new SocProduct();
        $form = $this->createForm(SocProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ($product->getFile() && $product->getFile()->getFile() === null) {
                $product->setFile(null);
            }

            if ($product->getImage() && $product->getImage()->getImageFile() === null) {
                $product->setImage(null);
            }
            $em->persist($product);
            $em->flush();

            //send product creation/update notification
            $bus->dispatch(new ProductUpdated($product->getId()));

            $this->addFlash('success', 'Product  "' . $product->getReferenceName() . '"created!');

            return $this->redirectToRoute('soc_product_index');
        }

        if ($form->isSubmitted() && $form->isValid() === false) {
            $this->addFlash('danger', 'Error saving changes');
            $errors = $form->getErrors(true, true);
            foreach ($errors as $error) {
                $this->addFlash('danger', $error->getMessage());
            }
        }

        return $this->render('soc_products_tree/new_edit.html.twig', [
           'product' => $product,
           'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/{id}/edit", name="edit", methods="GET|POST")
     * @param SocProduct $product
     * @param Request $request
     * @param MessageBusInterface $bus
     * @return RedirectResponse|Response
     * @throws \Knp\DoctrineBehaviors\Exception\ShouldNotHappenException
     */
    public function edit(SocProduct $product, Request $request, MessageBusInterface $bus)
    {
        if ($product->getTranslatedDocument() && !$product->getTranslatedDocument()->isEmpty()) {
            $this->migrateTranslatedDocumentIntoDocumentCollection($product, $this);
        }

        $deleteForm = $this->createDeleteForm($product);
        $form = $this->createForm(SocProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sendNotification = false;
            $continueEditingAfterSave = false;
            if ($product && $product->getParent() && $product->getId() === $product->getParent()->getId()) {
                $this->addFlash('warning', 'The product cant be its own parent');
            } else {
                if ($form->get('saveAndAddNotification')->isClicked()) {
                    $sendNotification = true;
                }
                if ($form->get('saveAndContinueEditing')->isClicked()) {
                    $continueEditingAfterSave = true;
                }

                $bus->dispatch(new ProductUpdated($product->getId(), '', $sendNotification));

                if ($sendNotification) {
                    $this->addFlash('success', 'A new notification was added for send to users.');
                }

                $this->addFlash('success', 'Product "' . $product->getReferenceName() . '" updated!');

                $product->updateTimestamps();

                $this->cleanEmptyDocumentFromCollection($product, $this);


                foreach ($product->getDocumentCollection() as $item) {
                    $item->setSocProduct($product);
                }

                $this->getDoctrine()->getManager()->persist($product);
                $this->getDoctrine()->getManager()->flush();

                if (!$continueEditingAfterSave) {
                    return $this->redirectToRoute('soc_product_index');
                }
            }
        }

        if ($form->isSubmitted() && $form->isValid() === false) {
            $this->addFlash('danger', 'Error saving changes');
            $errors = $form->getErrors(true, true);
            foreach ($errors as $error) {
                $this->addFlash('danger', $error->getMessage());
            }
        }

        return $this->render('soc_products_tree/new_edit.html.twig', [
           'product' => $product,
           'form' => $form->createView(),
           'deleteForm' => $deleteForm->createView(),
          ]);
    }



    /**
     * Creates a form to delete a booking entity.
     *
     * @param SocProduct $product
     * @return FormInterface
     */
    private function createDeleteForm(SocProduct $product): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('soc_product_delete', array('id' => $product->getId())))
            ->add('Delete', SubmitType::class, ['attr' => ['class' => 'btn btn-danger btn-delete']])
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Deletes a booking entity.
     *
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param SocProduct $product
     * @return RedirectResponse
     */
    public function delete(Request $request, SocProduct $product): RedirectResponse
    {
        $form = $this->createDeleteForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ($product->getSocProducts()->isEmpty()) {
                $em->remove($product);
                $wasError = false;
                try {
                    $em->flush();
                } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $e) {
                    $wasError = true;
                    $this->addFlash('danger', 'An exception occurred while deleting.
                    This product contains child products or documents that must be deleted first.');
                }
                if (!$wasError) {
                    $this->addFlash('success', 'Product  "' . $product->getReferenceName() . '" deleted!');
                }
            } else {
                $this->addFlash('danger', 'This product contains child products and can not be deleted.');
            }
        }

        return $this->redirectToRoute('soc_product_index');
    }

    public static function cleanEmptyDocumentFromCollection(SocProduct $product, AbstractController $controller): bool
    {
        $onMigration = false;
        foreach ($product->getDocumentCollection() as $item) {
            if ($item->isEmpty()) {
                if ($product->getDocumentCollection()->contains($item)) {
                    $product->getDocumentCollection()->removeElement($item);
                }

                $controller->getDoctrine()->getManager()->remove($item);

                $controller->getDoctrine()->getManager()->persist($product);
                $onMigration = true;
            }
        }

        if ($onMigration) {
            $controller->getDoctrine()->getManager()->flush();
        }
        return $onMigration;
    }

    public static function migrateTranslatedDocumentIntoDocumentCollection(
        SocProduct $product,
        AbstractController $controller
    ): bool
    {
        $onMigration = false;
        //Migrating from isolated document to documentCollection structure
        if ($product->getTranslatedDocument() && !$product->getTranslatedDocument()->isEmpty()) {
            $doc = $product->getTranslatedDocument();
            if (count($doc->getDocumentsLangs()) == 1) {
                $doc->setIsEnglishGlobalTranslation(true);
            }
            $product->addDocumentCollection($doc);
            
            $product->setTranslatedDocument(null);
            $controller->addFlash(
                'warning',
                'This product ('. $product->getId().' - '.$product->getReferenceName().') was updated automatically to include the linked document
                as one item of its Document Collection'
            );
            $onMigration = true;
        }

        //Migrating from 'Alternative translated document' to documentCollection
        /* If any document only for users with the 'alternative price' role
        this document will be append to documentCollection and set this group mark to it
        */
        if (
            $product->getAlternativeTranslatedDocument()
            && !$product->getAlternativeTranslatedDocument()->isEmpty()
        ) {
            /**
             * @var TranslatedDocument $alternativeDoc
             */
            $alternativeDoc = $product->getAlternativeTranslatedDocument();
            $alternativeDoc->setShowForGroups([UsersGroup::DEFAULT_HIDE_GROUP]);

            if (count($alternativeDoc->getDocumentsLangs()) == 1) {
                $alternativeDoc->setIsEnglishGlobalTranslation(true);
            }

            $product->addDocumentCollection($alternativeDoc);
            $product->setAlternativeTranslatedDocument(null);
            $controller->addFlash(
                'warning',
                'This product was updated automatically to include and alternative document
                        for a specific user group.
                        Please review the visibility of the documents is correct.'
            );
            $onMigration = true;
        }

        if ($onMigration) {
            $controller->getDoctrine()->getManager()->persist($product);
            $controller->getDoctrine()->getManager()->flush();
        }
        return $onMigration;
    }
}
