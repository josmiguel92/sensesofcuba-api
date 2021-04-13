<?php

namespace App\Controller;

use App\Entity\SocProduct;
use App\Entity\SocProductTranslation;
use App\EventSubscriber\OnJWTAuthenticationSuccess;
use App\Form\User\RegisterType;
use App\Repository\DocumentRepository;
use App\Repository\NewsRepository;
use App\Repository\SocProductRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @Route(host="%app.main_domain%")
 */
class ApiController extends AbstractController
{
    public const DATE_FORMAT = 'Y/m/d H:i:s';

    /**
     * @Route("/", name="homepage")
     * @throws NotFoundHttpException
     */
    public function homepage(): \Symfony\Component\HttpFoundation\Response
    {
     //   return new RedirectResponse('index.html');
         $file = __DIR__ . '/../../public/index.html';

        if (file_exists($file)) {
            return new Response(file_get_contents($file));
        }
        throw new NotFoundHttpException('file index.html Not Found.');
    }


    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): RedirectResponse
    {
        OnJWTAuthenticationSuccess::unsetCookie();

        return new RedirectResponse('index.html');
    }
    /**
     * @Route("/api/login", name="app_login")
     */
    public function login(): JsonResponse
    {
        return new JsonResponse([]);
    }


    /**
     * @Route("/api/products", name="api_products")
     * @param SocProductRepository $productRepository
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function apiProducts(
        Request $request,
        SocProductRepository $productRepository,
        UserRepository $userRepository
    ): JsonResponse {
        $user = null;
        $hiddenProducts = null;
        $useAlternativeDocuments = false;
        if ($username = $this->getUserNameFromCookie($request)) {
            $user = $userRepository->findOneBy(['email' => $username]);
        }
        if ($user !== null) {
            $hiddenProducts = $user->getHiddenProducts();

            if ($user->getRole() == 'ROLE_ALTERNATIVE_PRICES_CLIENT') {
                $useAlternativeDocuments = true;
            }
        }

        $products = $productRepository->findByEnabledOrdered();


        $lang = substr($request->headers->get('Accept-Language'), 0, 2);
        $lang = in_array($lang, ['en', 'es', 'de', 'fr']) ? $lang : 'en';

        $items = [];

        foreach ($products as $product) {
            if ($product instanceof  SocProductTranslation or $product === null) {
                continue;
            }

            if ($hiddenProducts && $hiddenProducts->contains($product)) {
                continue;
            }

            $currentIsAvailable = $product->isAvailableForLang($lang);
            if ($currentIsAvailable || !$product->getSocProducts()->isEmpty()) {
                if ($useAlternativeDocuments && $product->getAlternativeTranslatedDocumentFilePathByLang($lang)) {
                    $file = $product->getAlternativeTranslatedDocumentFilePathByLang($lang);
                } else {
                    $file = $product->getTranslatedDocumentFilePathByLang($lang);
                }

                $items[] = [
                    'id' => $product->getId(),
                    'title' => $product->getTranslatedNameOrReference($lang),
                    'description' =>  $product->getTranslatedDescOrNull($lang),
                    'file' => $file,
                    'modified_on' =>  $product->getUpdatedAt()->format(self::DATE_FORMAT),
                    'image' => $product->hasImage() ? 'uploads/images/' . $product->getImage()->getThumbnailPath() : null,
                    'child_of' => $product->getParent() ? $product->getParent()->getId() : null,
                    'subscribed' => $user ? $user->getSubscribedProducts()->contains($product) : false,
                ];
            }
        }

        // Some items with non-approved children are included. (The rule is: have children)
        // If this items are too non-approved (haven't description in current lang or downloadable file)
        // Those items must be removed on other foreach (maybe at render time)
        return new JsonResponse($items);
    }

    /**
     * @Route("/api/product_subscribe/{id}/{action}",
     *     name="api_product_subscribe",
     *     requirements={"action"="subscribe|unsubscribe"}
     *     )
     * @param SocProduct $product
     * @param $action
     * @param Request $request
     * @param UserRepository $userRepository
     * @param ManagerRegistry $managerRegistry
     * @return JsonResponse
     * @throws NotFoundHttpException
     */
    public function subscribeToProduct(
        SocProduct $product,
        $action,
        Request $request,
        UserRepository $userRepository,
        ManagerRegistry $managerRegistry
    ): JsonResponse {
        $user = null;
        if ($username = $this->getUserNameFromCookie($request)) {
            $user = $userRepository->findOneBy(['email' => $username]);
        }

        if (!$user || !$product) {
            throw new NotFoundHttpException();
        }

        if ($action === 'subscribe') {
            $product->addSubscribedUser($user);
        }
        if ($action === 'unsubscribe') {
            $product->removeSubscribedUser($user);
        }

        $em = $managerRegistry->getManager();
        $em->persist($product);
        $em->flush();

        return $this->json(['status' => 'product updated']);
    }

    public function getUserNameFromCookie($request)
    {
        $cookieStr = $request->cookies->get(OnJWTAuthenticationSuccess::$cookieName);
        if ($cookie = json_decode($cookieStr, true)) {
            return $cookie['username'];
        }
        return false;
    }


    /**
     * @Route("/api/documents", name="api_document")
     * @param DocumentRepository $documentRepository
     * @param Request $request
     * @return JsonResponse
     */
    public function apiDocuments(DocumentRepository $documentRepository, Request $request): JsonResponse
    {

        $lang = $this->getLangFromHeaders($request);

        $docs = $documentRepository->findBy(['enabled' => true], ['importance' => 'DESC']);

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
                'created_on' =>  $doc->getCreatedAt()->format(self::DATE_FORMAT),
                'modified_on' =>  $doc->getUpdatedAt()->format(self::DATE_FORMAT),
            ];
        }

        return new JsonResponse($items);
    }


    /**
     * @Route("/api/news", name="api_news")
     * @param NewsRepository $newsRepository
     * @param Request $request
     * @return JsonResponse
     */
    public function apiNews(NewsRepository $newsRepository, Request $request): JsonResponse
    {

        $lang = $this->getLangFromHeaders($request);

        $news = $newsRepository->findBy(['enabled' => true], ['createdAt' => 'DESC']);

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

            $updatedAt = $item->getCreatedOrUpdatedDate()->format(self::DATE_FORMAT);
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

    private function getLangFromHeaders($request): string
    {
        $lang = substr($request->headers->get('Accept-Language'), 0, 2);
        $lang = in_array($lang, ['en', 'es', 'de', 'fr']) ? $lang : 'en';
        return $lang;
    }
}
