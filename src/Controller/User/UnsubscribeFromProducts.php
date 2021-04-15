<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

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
        if ($user->getStaticUserHash() === $token) {
            $user->unsubscribeFromAllProducts();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->render('user/unsubscribed.html.twig', ['user' => $user]);
        }

        throw new NotFoundHttpException('wrong unsubscribe link');
    }
}
