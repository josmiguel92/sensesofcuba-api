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
    const DATE_FORMAT = 'Y/m/d H:i:s';
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
    public function api_products(Request $request,
                                 SocProductRepository $productRepository,
                                 UserRepository $userRepository): JsonResponse
    {
        $user = null;
        $hiddenProducts = null;
        if($username = $this->getUserNameFromCookie($request)) {
            $user = $userRepository->findOneBy(['email' => $username]);
        }
        if($user !== null) {
            $hiddenProducts = $user->getHiddenProducts();
        }

        $products = $productRepository->findBy(['enabled'=>true], ['importance'=>'DESC']);

        $lang = substr($request->headers->get('Accept-Language'), 0, 2);
        $lang = in_array($lang, ['en', 'es', 'de', 'fr']) ? $lang : 'en';

        $items = [];

        foreach ($products as $product)
        {
            if($hiddenProducts && $hiddenProducts->contains($product)) {
                continue;
            }

            if(!$product->getTranslatedDocument())
            {
                continue;
            }

            $fallbackEnglish = $product->isEnglishGlobalTranslation();
            if($lang !== 'en'
                && !$fallbackEnglish
                && $product->translate($lang, false)->getName() === null)
            {
                continue;
            }

            $file = null;

            if($product->getTranslatedDocument()->translate($lang, $fallbackEnglish)
                && $filename = $product->getTranslatedDocument()->translate($lang, $fallbackEnglish)->getFileName()) {
                $file = 'uploads/files/' . $filename;
            }

            $items[] = [
                'id' => $product->getId(),
                'title' => $product->translate($lang)->getName() ?: $product->getReferenceName(),
                'description' => $product->translate($lang)->getDescription(),
                'file' => $file,
                'modified_on' =>  $product->getUpdatedAt()->format(self::DATE_FORMAT),
                'image' => $product->hasImage() ? 'uploads/images/'.$product->getImage()->getThumbnailPath() : null,
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

            if(!$doc->getTranslatedDocument()) {
                continue;
            }

            $fallbackEnglish = $doc->isEnglishGlobalTranslation();
            if($lang !== 'en'
                && !$fallbackEnglish
                && $doc->translate($lang, false)->getName() === null)
            {
                continue;
            }


            $file = null;
            if(
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
     */
    public function subscribe_to_product(SocProduct $product,
                                         $action,
                                         Request $request,
                                         UserRepository $userRepository,
                                         ManagerRegistry $managerRegistry): JsonResponse
    {
        $user = null;
        if($username = $this->getUserNameFromCookie($request))
            $user = $userRepository->findOneBy(['email'=>$username]);

        if(!$user || !$product) {
            throw new NotFoundHttpException();
        }

        if($action === 'subscribe') {
            $product->addSubscribedUser($user);
        }
        if($action === 'unsubscribe')
        {
            $product->removeSubscribedUser($user);
        }

        $em = $managerRegistry->getManager();
        $em->persist($product);
        $em->flush();

        return $this->json(['status'=>'product updated']);
    }

    public function getUserNameFromCookie($request)
    {
        $cookieStr = $request->cookies->get(OnJWTAuthenticationSuccess::$cookieName);
        if($cookie = json_decode($cookieStr, true)) {
            return $cookie['username'];
        }
        return false;
    }
}
