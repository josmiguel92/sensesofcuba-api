<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use App\Http\Responder;
use App\Http\RespondRouteRedirect;
use App\Message\ConfirmUserAccount;
use MsgPhp\User\Command\ConfirmUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/register/confirm/{token}", name="confirm_registration", host="%app.main_domain%")
 */
final class ConfirmRegistrationController
{
    /**
     * @ParamConverter("user", converter="doctrine.orm", options={"mapping": {"token": "confirmationToken"}})
     */
    public function __invoke(
        User $user,
        Responder $responder,
        MessageBusInterface $bus,
        Environment $templating
    ): Response {
        $bus->dispatch(new ConfirmUserAccount($user->getId()));

        return new Response($templating->render('user/activated_account.html.twig', ['user'=>$user]));
//        return $responder->respond((new RespondRouteRedirect('homepage')));
    }
}
