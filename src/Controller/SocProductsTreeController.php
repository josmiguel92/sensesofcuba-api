<?php

namespace App\Controller;

use App\Entity\SocProduct;
use App\Form\SocProductType;
use App\Message\Events\ProductUpdated;
use App\Repository\SocProductRepository;
use App\SocMimeTypeGuesser;
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
            'products' => $productRepository->findBy(['parent'=>null])
        ]);
    }

    /**
     * @Route("/new", name="new", methods="GET|POST")
     * @param Request $request
     * @param MimeTypeGuesserInterface $guesser
     * @return RedirectResponse|Response
     */
    public function new(Request $request)
    {
        $product = new SocProduct();
        $form = $this->createForm(SocProductType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();

            if($product->getFile() && $product->getFile()->getFile() === null)
                $product->setFile(null);

            if($product->getImage() && $product->getImage()->getImageFile() === null)
                $product->setImage(null);
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Product created!');

            return $this->redirectToRoute('soc_product_index');
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
     * @return RedirectResponse|Response
     */
    public function edit(SocProduct $product, Request $request, MessageBusInterface $bus)
    {
        $deleteForm = $this->createDeleteForm($product);
        $form = $this->createForm(SocProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Product updated!');
            $bus->dispatch(new ProductUpdated($product->getId()));


            return $this->redirectToRoute('soc_product_index');
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
            $em->remove($product);
            $em->flush();
        }

        return $this->redirectToRoute('soc_product_index');
    }
}
