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
 * @Route("/api", host="%app.main_domain%", name="api_")
 */
class ApiController extends AbstractController
{
    public const DATE_FORMAT = 'Y/m/d H:i:s';


    public static function getLangFromHeaders($request): string
    {
        $lang = substr($request->headers->get('Accept-Language'), 0, 2);
        return in_array($lang, ['en', 'es', 'de', 'fr']) ? $lang : 'en';
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(): JsonResponse
    {
        return new JsonResponse([]);
    }

    public static function getUserNameFromCookie($request)
    {
        $cookieStr = $request->cookies->get(OnJWTAuthenticationSuccess::$cookieName);
        if ($cookie = json_decode($cookieStr, true)) {
            return $cookie['username'];
        }
        return false;
    }

}
