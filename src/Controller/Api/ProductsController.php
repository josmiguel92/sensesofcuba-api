<?php

namespace App\Controller\Api;

use App\DataTransformer\ProductViewConfigurator;
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
 * @Route("/api/products", host="%app.main_domain%", name="api_products_")
 */
class ProductsController extends AbstractController
{

    /**
     * @Route("/", name="index")
     * @param SocProductRepository $productRepository
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function index(
        Request $request,
        SocProductRepository $productRepository,
        UserRepository $userRepository,
        \Vich\UploaderBundle\Templating\Helper\UploaderHelper $uploaderHelper
    ): JsonResponse {
        $user = null;
        $hiddenProducts = null;
        $useAlternativeDocuments = false;
        if ($username = $this->getUser()->getUsername()) {
            $user = $userRepository->findOneBy(['email' => $username]);
        }
        if ($user !== null) {
            $hiddenProducts = $user->getHiddenProducts();

            if (
                $user->getUserGroup()->contains('alternative-prices')
                ||
                $user->getRole() == 'ROLE_ALTERNATIVE_PRICES_CLIENT'
            ) {
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

            $productView = new ProductViewConfigurator($product, $user, $lang, $uploaderHelper);

            if ($productView->isAllowedToShow()) {
                $items[] = $productView->formatAsArray();
            }
        }

        // Some items with non-approved children are included. (The rule is: have children)
        // If this items are too non-approved (haven't description in current lang or downloadable file)
        // Those items must be removed on other foreach (maybe at render time)
        return new JsonResponse($items);
    }

    /**
     * @Route("/subscribe/{id}/{action}",
     *     name="subscribe",
     *     requirements={"action"="subscribe|unsubscribe"}
     *     )
     * @param SocProduct $product
     * @param string $action
     * @param UserRepository $userRepository
     * @param ManagerRegistry $managerRegistry
     * @return JsonResponse
     */
    public function subscribeToProduct(
        SocProduct $product,
        string $action,
        UserRepository $userRepository,
        ManagerRegistry $managerRegistry
    ): JsonResponse {
        $user = null;
        if ($username = $this->getUser()->getUsername()) {
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
}
