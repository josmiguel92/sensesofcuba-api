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
 * @Route("/api/news", host="%app.main_domain%", name="api_news_")
 */
class NewsController extends AbstractController
{

    /**
     * @Route("/", name="index")
     * @param NewsRepository $newsRepository
     * @param Request $request
     * @return JsonResponse
     */
    public function index(NewsRepository $newsRepository, Request $request): JsonResponse
    {
        $lang = ApiController::getLangFromHeaders($request);

        $news = $newsRepository->findBy(['enabled' => true], ['updatedAt' => 'DESC']);

        $items = [];

        foreach ($news as $item) {
            $fallbackEnglish = $item->isEnglishGlobalTranslation();
            if (
                $lang !== 'en'
                && !$fallbackEnglish
                && $item->translate($lang, false)->getTitle() === null
            ) {
                continue;
            }


            $file = null;
            if (
                $item->getTranslatedDocument()
                &&
                $item->getTranslatedDocument()->translate($lang, $fallbackEnglish)
                &&
                $filename = $item->getTranslatedDocument()->translate($lang, $fallbackEnglish)->getFileName()
            ) {
                $file = "uploads/files/" . $filename;
            }

            $updatedAt = $item->getCreatedOrUpdatedDate()->format(ApiController::DATE_FORMAT);
            $items[] = [
                'id' => $item->getId(),
                'title' => $item->translate($lang)->getTitle() ?: $item->getReferenceName(),
                'description' => $item->translate($lang)->getDescription(),
                'file' => $file,
                'modified_on' =>  $updatedAt,
            ];
        }

        return new JsonResponse($items);
    }


}
