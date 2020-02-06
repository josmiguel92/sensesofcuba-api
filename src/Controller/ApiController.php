<?php

namespace App\Controller;

use App\Entity\SocProduct;
use App\EventSubscriber\OnJWTAuthenticationSuccess;
use App\Form\User\RegisterType;
use App\Repository\DocumentRepository;
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
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @Route(host="%app.main_domain%")
 */
class ApiController extends AbstractController
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
    public function api_products(SocProductRepository $productRepository, Request $request, UserRepository $userRepository): JsonResponse
    {
        $user = null;
        $hiddenProducts = null;
        if($username = $this->getUserNameFromCookie($request))
            $user = $userRepository->findOneBy(['email'=>$username]);
        if($user)
            $hiddenProducts = $user->getHiddenProducts();

        $products = $productRepository->findBy(['enabled'=>true], ['importance'=>'DESC']);
        //dump($categories);
        $lang = substr($request->headers->get('Accept-Language'), 0, 2);
        $lang = in_array($lang, ['en', 'es', 'de', 'fr']) ? $lang : 'en';

        $items = [];

        foreach ($products as $product)
        {
            if($hiddenProducts && $hiddenProducts->contains($product)) {
                continue;
            }

            $file = null;
            if($product->getTranslatedDocument() && $product->getTranslatedDocument()->translate($lang)) {
                $file = 'uploads/files/' . $product->getTranslatedDocument()->translate($lang)->getFileName();
            }
            $items[] = [
                'id' => $product->getId(),
                'title' => $product->translate($lang)->getName() ?: $product->getReferenceName(),
                'description' => $product->translate($lang)->getDescription(),
                'file' => $file,
                'modified_on' =>  $product->getUpdatedAt(),
                'image' => $product->hasImage() ? 'uploads/images/'.$product->getImage()->getImageName() : null,
                'child_of' => $product->getParent() ? $product->getParent()->getId() : null,
                'subscribed' => $product->getSubscribedUsers()->contains($user),
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

        $docs = $documentRepository->findBy(['enabled'=>true], ['importance'=>'DESC']);


        $items = [];

        foreach ($docs as $doc)
        {

            $file = null;
            if($doc->getTranslatedDocument() && $doc->getTranslatedDocument()->translate($lang)) {
                $file = "uploads/files/" . $doc->getTranslatedDocument()->translate($lang)->getFileName();
            }

            $items[] = [
                'id' => $doc->getId(),
                'title' => $doc->translate($lang)->getName() ?: $doc->getReferenceName(),
                'description' => $doc->translate($lang)->getDescription(),
                'file' => $file,
                'created_on' =>  $doc->getCreatedAt(),
                'modified_on' =>  $doc->getUpdatedAt(),
            ];
        }

        return new JsonResponse($items);
    }

    /**
     * @Route("/api/product_subscribe/{id}/{action}", name="api_product_subscribe")
     * @param SocProduct $product
     * @param $action
     * @param UserRepository $userRepository
     * @param Request $request
     * @return JsonResponse
     */
    public function subscribe_to_product(SocProduct $product, $action, UserRepository $userRepository, Request $request, ManagerRegistry $managerRegistry): JsonResponse
    {
        $user = null;
        if($username = $this->getUserNameFromCookie($request))
            $user = $userRepository->findOneBy(['email'=>$username]);

        if(!$user || !$product)
            throw new NotFoundHttpException();

        if($action === 'subscribe')
        $product->addSubscribedUser($user);
        else $product->removeSubscribedUser($user);

        $em = $managerRegistry->getManager();
        $em->persist($product);
        $em->flush();

        return $this->json(['status'=>'product updated']);
    }

    public function getUserNameFromCookie($request)
    {
        $cookieStr = $request->cookies->get(OnJWTAuthenticationSuccess::$cookieName);
        if($cookie = json_decode($cookieStr, true))
            return $cookie['username'];
        return false;
    }
}
