<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use App\Form\User\ResetPasswordType;
use App\Message\Events\PasswordReset;
use Doctrine\ORM\EntityManagerInterface;
use MsgPhp\User\Command\ResetUserPassword;
use MsgPhp\User\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;


final class UnsubscribeFromProducts extends AbstractController
{
    /**
     * @Route("/products-updates-unsubscribe/{email}/{token}", name="products_updates_unsubscribe", host="%app.main_domain%")
     * @ParamConverter("user", converter="doctrine.orm", options={"mapping": {"email": "email"}})
     * @param User $user
     * @param $token
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function unsubscribeUserFromAllProducts(User $user, $token, EntityManagerInterface $entityManager): Response
    {
        if($user->getStaticUserHash() === $token)
        {
            $user->unsubscribeFromAllProducts();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->render('user/unsubscribed.html.twig', ['user'=>$user]);
        }
        else throw new NotFoundHttpException('wrong unsubscribe link');

    }
}
