<?php

namespace App\Controller;

use App\Repository\DocumentRepository;
use App\Repository\ProductCategoryRepository;
use App\Repository\SocProductRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage(): \Symfony\Component\HttpFoundation\Response
    {
     //   return new RedirectResponse('index.html');
         $file = __DIR__."/../../public/dist/index.html";

    if (file_exists($file)) {
        return new Response(file_get_contents($file));
    } else {
        throw new NotFoundHttpException("file index.html Not Found.");
    }
    }


    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): RedirectResponse
    {
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
            $items[] = [
                'id' => $product->getId(),
                'title' => $product->translate($lang)->getName(),
                'description' => $product->translate($lang)->getDescription(),
                'file' => $product->getTranslatedDocument() ? 'uploads/files/'.$product->getTranslatedDocument()->translate($lang)->getFileName() :  null,
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
            $items[] = [
                'id' => $doc->getId(),
                'title' => $doc->translate($lang)->getName(),
                'description' => $doc->translate($lang)->getDescription(),
                'file' => $doc->getFile() ? 'uploads/files/'.$doc->getFile()->getFileName() :  null,
                'created_on' =>  $doc->getCreatedAt(),
                'modified_on' =>  $doc->getUpdatedAt(),
                'image' => $doc->getImage() ? 'uploads/images/'.$doc->getImage()->getImageName() : null
            ];
        }

        return new JsonResponse($items);
    }

    /**
     * @Route("/api/reset-password", name="api_reset_password")
     * @return JsonResponse
     */
    public function api_reset_password(Request $request, UserRepository $userRepository): JsonResponse
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            return new JsonResponse(null, 200);
        }
    }


}
