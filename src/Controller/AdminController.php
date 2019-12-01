<?php

namespace App\Controller;

use App\Repository\DocumentRepository;
use App\Repository\ProductCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function MongoDB\BSON\fromJSON;

class AdminController extends AbstractController
{
    /**
     * @Route("/home", name="homepage")
     */
    public function homepage(): \Symfony\Component\HttpFoundation\Response
    {
        return new RedirectResponse('index.html');
    }


    /**
     * @Route("/test", name="test")
     */
    public function test(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render("admin/index.html.twig");
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
     * @param ProductCategoryRepository $categoryRepository
     * @return JsonResponse
     */
    public function api_products(ProductCategoryRepository $categoryRepository): JsonResponse
    {
        $categories = $categoryRepository->findAll();
        //dump($categories);

        $items = [];
        $category_id_str = 'cat-';
        $product_id_str = 'prod-';

        foreach ($categories as $category)
        {


            $items[] = [
                'id' => $category_id_str.$category->getId(),
                'title' => $category->getTitle(),
                'file' => null,
                'created_on' =>  $category->getCreatedAt(),
                'modified_on' =>  $category->getUpdatedAt(),
                'image' => $category->getImageWebPath(),
                'child_of' => $category->getParent() ? $category_id_str.$category->getParent()->getId() : null
            ];

            foreach ($products = $category->getProducts() as $product)
            {
                $items[] = [
                    'id' => $product_id_str.$product->getId(),
                    'title' => $product->getTitle(),
                    'file' => $product->getFileWebPath(),
                    'created_on' =>  $product->getCreatedAt(),
                    'modified_on' =>  $product->getUpdatedAt(),
                    'image' => $product->getImageWebPath(),
                    'child_of' => $product->getCategory() ? $category_id_str.$product->getCategory()->getId() : null,
                ];
            }
        }

        return new JsonResponse($items);
    }


    /**
     * @Route("/api/documents", name="api_document")
     * @param DocumentRepository $documentRepository
     * @return JsonResponse
     */
    public function api_document(DocumentRepository $documentRepository): JsonResponse
    {
        $docs = $documentRepository->findAll();


        $items = [];

        foreach ($docs as $doc)
        {


            $items[] = [
                'id' => $doc->getId(),
                'title' => $doc->getTitle(),
                'file' => $doc->getFileWebPath(),
                'created_on' =>  $doc->getCreatedAt(),
                'modified_on' =>  $doc->getUpdatedAt(),
                'image' => $doc->getImageWebPath()
            ];

        }

        return new JsonResponse($items);
    }



    /**
     * @Route("/api/contact", name="api_contact")
     * @return JsonResponse
     */
    public function api_contact(): JsonResponse
    {

        $contactInfo = <<<EOF
{
    "opening_times": [
        {
            "day_range": "Monday - Friday",
            "hour_range": "09:00 - 17:00"
        },
        {
            "day_range": "Saturday - Sunday (and public holidays)",
            "hour_range": "10:00 - 16:00"
        }
    ],
    "emails": [
        {
            "label": "General Contact",
            "address": "info@sensesofcuba.com"
        },
        {
            "label": "Sales",
            "address": "info@sensesofcuba.com"
        },
        {
            "label": "Product Management",
            "address": "info@sensesofcuba.com"
        }
    ],
    "phones": [
        {
            "label": "General",
            "number": "+53 7866 4734"
        },
        {
            "label": "Emergency",
            "number": "+53 7866 4734"
        }
    ],
    "addresses": [
        {
            "address": "Edificio Bacardi, Oficina 404, Calle Monserrate 261, CP 10100, La Habana Vieja, Habana, Cuba"
        }
    ]
}
EOF;

        return JsonResponse::fromJsonString($contactInfo)
            ->setSharedMaxAge(300);

    }





}
