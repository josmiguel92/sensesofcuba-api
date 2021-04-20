<?php

namespace App\Controller\Api;

use App\Entity\SocProduct;
use App\Entity\SocProductTranslation;
use App\EventSubscriber\OnJWTAuthenticationSuccess;
use App\Repository\DocumentRepository;
use App\Repository\NewsRepository;
use App\Repository\SocProductRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/documents", host="%app.main_domain%", name="api_documents_")
 */
class DocumentsController extends AbstractController
{


    /**
     * @Route("/", name="index")
     * @param DocumentRepository $documentRepository
     * @param Request $request
     * @return JsonResponse
     */
    public function apiDocuments(DocumentRepository $documentRepository, Request $request): JsonResponse
    {
        $lang = ApiController::getLangFromHeaders($request);

        $docs = $documentRepository->findBy(['enabled' => true], ['importance' => 'DESC', 'updatedAt' => 'DESC' ]);

        $items = [];

        foreach ($docs as $doc) {
            if (!$doc->getTranslatedDocument()) {
                continue;
            }

            $fallbackEnglish = $doc->isEnglishGlobalTranslation();
            if (
                $lang !== 'en'
                && !$fallbackEnglish
                && $doc->translate($lang, false)->getName() === null
            ) {
                continue;
            }


            $file = null;
            if (
                $doc->getTranslatedDocument()->translate($lang, $fallbackEnglish)
                &&
                $filename = $doc->getTranslatedDocument()->translate($lang, $fallbackEnglish)->getFileName()
            ) {
                $file = "uploads/files/" . $filename;
            }

            $items[] = [
                'id' => $doc->getId(),
                'title' => $doc->translate($lang)->getName() ?: $doc->getReferenceName(),
                'description' => $doc->translate($lang)->getDescription(),
                'file' => $file,
                'created_on' =>  $doc->getCreatedAt()->format(ApiController::DATE_FORMAT),
                'modified_on' =>  $doc->getUpdatedAt()->format(ApiController::DATE_FORMAT),
            ];
        }

        return new JsonResponse($items);
    }

}
