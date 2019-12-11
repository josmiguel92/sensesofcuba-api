<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\SocProduct;
use App\Form\DocumentType;
use App\Form\SocProductType;
use App\Repository\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
* @Route("/admin/document", name="soc_document_")
*/
class SocDocumentsController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(DocumentRepository $documentRepository)
    {
        return $this->render('soc_documents/index.html.twig', [
            'documents' => $documentRepository->findAll()
        ]);
    }

    /**
     * @Route("/new", name="new", methods="GET|POST")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function new(Request $request)
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();

            if($document->getFile() && $document->getFile()->getFile() === null)
                $document->setFile(null);

            if($document->getImage() && $document->getImage()->getImageFile() === null)
                $document->setImage(null);
            $em->persist($document);
            $em->flush();

            $this->addFlash('success', 'Document created!');

            return $this->redirectToRoute('soc_document_index');
        }

        return $this->render('soc_documents/new_edit.html.twig', [
           'document' => $document,
           'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/{id}/edit", name="edit", methods="GET|POST")
     * @param Document $document
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit(Document $document, Request $request)
    {
        $deleteForm = $this->createDeleteForm($document);
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($document->getFile() && $document->getFile()->getFile() === null)
                $document->setFile(null);

            if($document->getImage() && $document->getImage()->getImageFile() === null)
                $document->setImage(null);

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Document updated!');

            return $this->redirectToRoute('soc_document_index');
        }

          return $this->render('soc_documents/new_edit.html.twig', [
           'document' => $document,
           'form' => $form->createView(),
           'deleteForm' => $deleteForm->createView(),
        ]);

    }


    /**
     * Creates a form to delete a booking entity.
     *
     * @param Document $document
     * @return FormInterface
     */
    private function createDeleteForm(Document $document): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('soc_document_delete', array('id' => $document->getId())))
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
     * @param Document $document
     * @return RedirectResponse
     */
    public function delete(Request $request, Document $document): RedirectResponse
    {
        $form = $this->createDeleteForm($document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($document);
            $em->flush();
        }

        return $this->redirectToRoute('soc_document_index');
    }


}
