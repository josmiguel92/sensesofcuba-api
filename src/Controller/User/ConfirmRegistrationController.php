<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use App\Http\Responder;
use App\Http\RespondRouteRedirect;
use MsgPhp\User\Command\ConfirmUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/register/confirm/{token}", name="confirm_registration")
 */
final class ConfirmRegistrationController
{
    /**
     * @ParamConverter("user", converter="doctrine.orm", options={"mapping": {"token": "confirmationToken"}})
     */
    public function __invoke(
        User $user,
        Responder $responder,
        MessageBusInterface $bus
    ): Response {
        $bus->dispatch(new ConfirmUser($user->getId()));

        return $responder->respond((new RespondRouteRedirect('login'))->withFlashes([
            'success' => sprintf('Hi %s, your registration is confirmed. You can now login.', $user->getEmail()),
        ]));
    }
}
