<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use App\Form\User\ForgotPasswordType;
use App\Message\PasswordRequest;
use App\Repository\UserRepository;
use MsgPhp\User\Command\RequestUserPassword;
use MsgPhp\User\Event\UserPasswordRequested;
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
 * @Route("/api/reset-password", name="api_reset_password", methods={"POST", "GET"}, host="%app.main_domain%")
 */
class ForgotPasswordController extends AbstractController
{
    public function __invoke(
        Request $request,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        Environment $twig,
        MessageBusInterface $bus,
        UserRepository $userRepository
    ): Response {

        if($request->isMethod(Request::METHOD_GET))
        {
            return $this->json(['_token'=>RegisterController::getToken()]);
        }

        $data = json_decode($request->getContent(), true);

        $form = $formFactory->create(ForgotPasswordType::class);

        $dataNormalized = [];
        if ($data) {
            foreach($data as $key => $value) {
                $dataNormalized[$key] = $value;
            }
        }

        if(!RegisterController::isValidToken($data['_token'])){
            return $this->json(['_error' => "Invalid CSRF Token"], 400);
        }

        $form->submit($dataNormalized);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if (isset($data['email'])) {
                /** @var User $user */

                $user = $userRepository->findOneBy(['email'=>$data['email']]);
                if($user)
                {
                    $bus->dispatch(new PasswordRequest($user->getId()));
                    $bus->dispatch(new RequestUserPassword($user->getId()));
                    return $this->json(['message' => 'Succeed']);
                }
            }

        }
        return  $this->json(['message' => 'Error'], Response::HTTP_BAD_REQUEST);
    }
}
