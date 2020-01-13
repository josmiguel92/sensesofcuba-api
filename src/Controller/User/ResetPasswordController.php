<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use App\Form\User\ResetPasswordType;
use App\Message\PasswordReseted;
use MsgPhp\User\Command\ResetUserPassword;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

/**
 * @Route("/reset-password/{token}", name="reset_password")
 */
final class ResetPasswordController extends AbstractController
{
    /**
     * @ParamConverter("user", converter="doctrine.orm", options={"mapping": {"token": "passwordResetToken"}})
     */
    public function __invoke(
        User $user,
        Request $request,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        Environment $twig,
        MessageBusInterface $bus
    ): Response {
        $form = $formFactory->createNamed('', ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bus->dispatch(new ResetUserPassword($user->getId(), $form->getData()['password']));
            $bus->dispatch(new PasswordReseted($user->getEmail()));
            $flashBag->add('success', 'You\'re password is changed.');

            return $this->redirectToRoute('homepage');
        }

        return new Response($twig->render('user/reset_password.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
