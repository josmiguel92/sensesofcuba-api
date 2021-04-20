<?php

namespace App\Controller;

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
 * @Route(host="%app.main_domain%")
 */
class DefaultController extends AbstractController
{
    public const DATE_FORMAT = 'Y/m/d H:i:s';

    /**
     * @Route("/", name="homepage")
     * @throws \Exception
     */
    public function homepage(): \Symfony\Component\HttpFoundation\Response
    {
        //   return new RedirectResponse('index.html');
        $file = __DIR__ . '/../../public/index.html';

        if (file_exists($file)) {
            return new Response(file_get_contents($file));
        }
        throw new \Exception('file index.html Not Found.', 500);
    }


    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): RedirectResponse
    {
        OnJWTAuthenticationSuccess::unsetCookie();

        return new RedirectResponse('index.html');
    }


}
