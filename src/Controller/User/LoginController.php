<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Form\User\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

/**
 * @Route("/login", name="login", host="%app.main_domain%")
 */
final class LoginController extends AbstractController
{
    public function __invoke(
        Environment $twig,
        FormFactoryInterface $formFactory,
        AuthenticationUtils $authenticationUtils
    ): Response {

        return $this->redirectToRoute('homepage');

//        $form = $formFactory->createNamed('', LoginType::class, [
//            'email' => $authenticationUtils->getLastUsername(),
//        ]);
//
//        if (null !== $error = $authenticationUtils->getLastAuthenticationError(true)) {
//            $form->addError(new FormError($error->getMessage(), $error->getMessageKey(), $error->getMessageData()));
//        }
//
//        return new Response($twig->render('user/login.html.twig', [
//            'form' => $form->createView(),
//        ]));
    }
}
