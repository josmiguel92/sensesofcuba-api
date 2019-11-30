<?php

namespace App\Controller;

use App\Repository\ProductCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/home", name="homepage")
     */
    public function homepage(): \Symfony\Component\HttpFoundation\Response
    {
        return new RedirectResponse("index.html");
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
    public function logout()
    {
        return new RedirectResponse("index.html");
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
                    'child_of' => $product->getCategory() ? $product.$product->getCategory()->getId() : null,
                ];
            }
        }

        return new JsonResponse($items);
    }





}
