<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\News;
use App\Entity\SocProduct;
use App\Form\DocumentType;
use App\Form\SocProductType;
use App\Repository\DocumentRepository;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\MimeTypeGuesserInterface;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Routing\Annotation\Route;

/**
* @Route("/admin/news", name="soc_news_", host="%app.main_domain%")
*/
class SocNewsController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param NewsRepository $newsRepository
     * @return Response
     */
    public function index(NewsRepository $newsRepository)
    {
        return $this->render('soc_documents/index.html.twig', [
            'documents' => $newsRepository->findAll(),
            'className' => 'News'
        ]);
    }

    /**
     * @Route("/new", name="new", methods="GET|POST")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function new(Request $request)
    {
        $news = new News();
        $form = $this->createForm(DocumentType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($news);
            $em->flush();

            $this->addFlash('success', 'News "' . $news->getReferenceName() . '" created!');

            return $this->redirectToRoute('soc_news_index');
        }

        return $this->render('soc_documents/new_edit.html.twig', [
           'document' => $news,
           'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/{id}/edit", name="edit", methods="GET|POST")
     * @param News $news
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit(News $news, Request $request)
    {
        $deleteForm = $this->createDeleteForm($news);
        $form = $this->createForm(DocumentType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'News "' . $news->getReferenceName() . '" updated!');

            return $this->redirectToRoute('soc_document_index');
        }
        if ($form->isSubmitted() && $form->isValid() === false) {
            $this->addFlash('danger', 'Error saving changes');
            $errors = $form->getErrors(true, true);
            foreach ($errors as $error) {
                $this->addFlash('danger', $error->getMessage());
            }
        }

          return $this->render('soc_documents/new_edit.html.twig', [
           'document' => $news,
           'form' => $form->createView(),
           'deleteForm' => $deleteForm->createView(),
          ]);
    }


    /**
     * Creates a form to delete a booking entity.
     *
     * @param News $news
     * @return FormInterface
     */
    private function createDeleteForm(News $news): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('soc_news_delete', array('id' => $news->getId())))
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
     * @param News $news
     * @return RedirectResponse
     */
    public function delete(Request $request, News $news): RedirectResponse
    {
        $form = $this->createDeleteForm($news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($news);
            $em->flush();
        }

        return $this->redirectToRoute('soc_news_index');
    }
}
