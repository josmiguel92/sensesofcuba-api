<?php

namespace App\Controller;

use App\Entity\SocProduct;
use App\EntityAudition\EntityAuditor;
use App\Form\SocProductType;
use App\Message\Events\ProductUpdated;
use App\Repository\SocProductRepository;
use Doctrine\ORM\UnitOfWork;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\MimeTypeGuesserInterface;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Mime\MimeTypesInterface;
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
     */
    public function edit(SocProduct $product, Request $request, MessageBusInterface $bus)
    {
        $deleteForm = $this->createDeleteForm($product);
        $form = $this->createForm(SocProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($product && $product->getParent() && $product->getId() === $product->getParent()->getId()) {
                $this->addFlash('warning', 'The product parent is not valid. It must be different than himself');
            } else {
                $em = $this->getDoctrine()->getManager();
                $uow = $em->getUnitOfWork();
                $auditor = new EntityAuditor($em, $uow);

                if ($auditor->areUpdates()) {
                    $bus->dispatch(new ProductUpdated($product->getId(), $auditor->getFormattedDiffStr()));
                    $this->addFlash('success', 'Product "' . $product->getReferenceName() . '" updated!');
                } else {
                    $this->addFlash('warning', 'There are not changes on "' . $product->getReferenceName() . '"!');
                }

                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('soc_product_index');
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
}
