<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use App\Form\User\ForgotPasswordType;
use MsgPhp\User\Command\RequestUserPassword;
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
 * @Route("/api/reset-password", name="reset_password", methods={"POST"})
 */
final class ForgotPasswordController extends AbstractController
{
    public function __invoke(
        Request $request,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        Environment $twig,
        MessageBusInterface $bus
    ): Response {
        $form = $formFactory->createNamed('', ForgotPasswordType::class);

        if ($request->isMethod(Request::METHOD_POST)) {
            $data = json_decode($request->getContent(), true);
            if ($data) {
                foreach($data as $key => $value) {
                    $request->request->add([$key => $value]);
                }
            }
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                if (isset($data['user'])) {
                    /** @var User $user */
                    $user = $data['user'];
                    $bus->dispatch(new RequestUserPassword($user->getId()));
                }
                return $this->json(['message' => 'Succeed']);
            }
        }

        //$form->handleRequest($request);

        //if ($form->isSubmitted() && $form->isValid()) {
        //    $data = $form->getData();

        //    if (isset($data['user'])) {
        //        /** @var User $user */
        //        $user = $data['user'];
        //        $bus->dispatch(new RequestUserPassword($user->getId()));
        //    }

        //    $flashBag->add('success', 'You\'re password is requested.');

        //    return new RedirectResponse('/login');
        //}

        //return new Response($twig->render('user/forgot_password.html.twig', [
        //    'form' => $form->createView(),
        //]));
    }
}
