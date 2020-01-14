<?php

namespace App\Controller;

use App\Entity\SocProduct;
use App\EventSubscriber\OnJWTAuthenticationSuccess;
use App\Form\User\RegisterType;
use App\Repository\DocumentRepository;
use App\Repository\ProductCategoryRepository;
use App\Repository\SocProductRepository;
use App\Repository\UserRepository;
use http\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class AdminController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage(): \Symfony\Component\HttpFoundation\Response
    {
     //   return new RedirectResponse('index.html');
         $file = __DIR__. '/../../public/index.html';

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
        setcookie(OnJWTAuthenticationSuccess::$cookieName, '', time() - 3600);
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
     * @return JsonResponse
     */
    public function api_products(SocProductRepository $productRepository, Request $request): JsonResponse
    {
        $products = $productRepository->findBy(['enabled'=>true]);
        //dump($categories);
        $lang = substr($request->headers->get('Accept-Language'), 0, 2);
        $lang = in_array($lang, ['en', 'es', 'de', 'fr']) ? $lang : 'en';

        $items = [];

        foreach ($products as $product)
        {
            $file = null;
            if($product->getTranslatedDocument() && $product->getTranslatedDocument()->translate($lang)) {
                $file = 'uploads/files/' . $product->getTranslatedDocument()->translate($lang)->getFileName();
            }
            $items[] = [
                'id' => $product->getId(),
                'title' => $product->translate($lang)->getName(),
                'description' => $product->translate($lang)->getDescription(),
                'file' => $file,
                'modified_on' =>  $product->getUpdatedAt(),
                'image' => $product->getImage() ? 'uploads/images/'.$product->getImage()->getImageName() : null,
                'child_of' => $product->getParent() ? $product->getParent()->getId() : null,
            ];
        }

        return new JsonResponse($items);
    }


    /**
     * @Route("/api/documents", name="api_document")
     * @param DocumentRepository $documentRepository
     * @return JsonResponse
     */
    public function api_document(DocumentRepository $documentRepository, Request $request): JsonResponse
    {

        $lang = substr($request->headers->get('Accept-Language'), 0, 2);
        $lang = in_array($lang, ['en', 'es', 'de', 'fr']) ? $lang : 'en';

        $docs = $documentRepository->findBy(['enabled'=>true]);


        $items = [];

        foreach ($docs as $doc)
        {

            $file = null;
            if($doc->getTranslatedDocument() && $doc->getTranslatedDocument()->translate($lang)) {
                $file = "uploads/files/" . $doc->getTranslatedDocument()->translate($lang)->getFileName();
            }

            $items[] = [
                'id' => $doc->getId(),
                'title' => $doc->translate($lang)->getName(),
                'description' => $doc->translate($lang)->getDescription(),
                'file' => $file,
                'created_on' =>  $doc->getCreatedAt(),
                'modified_on' =>  $doc->getUpdatedAt(),
                'image' => $doc->getImage() ? 'uploads/images/'.$doc->getImage()->getImageName() : null
            ];
        }

        return new JsonResponse($items);
    }
}
