<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Form\User\RegisterType;
use App\Message\CreateUser;
use App\Message\Events\UserRegistered;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

/**
 * @Route("/api/register", name="register", methods={"POST", "GET"}, host="%app.main_domain%")
 */
final class RegisterController extends AbstractController
{
    public function __invoke(
        Request $request,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        Environment $twig,
        MessageBusInterface $bus
    ): Response {

        $form = $this->createForm(RegisterType::class);
        $form->handleRequest($request);

        try {
            $data = json_decode($request->getContent(), true);
        } catch (\JsonException $e) {
            $data = null;
        }

        $form->submit($data);


        if ($form->isSubmitted() && $form->isValid()) {
            $bus->dispatch(new CreateUser($form->getData()));
            $flashBag->add('success', 'You\'re successfully registered.');

            return $this->json(['message' => 'You\'re successfully registered. We\'ve send you an email about it']);
        }

        $token = $form->createView()->children['_token']->vars['value'] ?? null;

        return $this->json(['_token' => $token]);



        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            if (is_array($error->getMessageParameters()) && count($error->getMessageParameters()) > 0) {
//                    dump($error->getMessageParameters());
                $value = array_values($error->getMessageParameters())[0];
                $errors[] = $error->getMessageTemplate() . ': ' . $value;
            }
        }
        return $this->json(['message' => 'The form contains errors', 'errors' => $errors], Response::HTTP_BAD_REQUEST);
    }

    public static function getToken(): string
    {
        return substr(uniqid("t", true), 0, 10) . '-' . self::getTokenKeepStr();
    }

    private static function getTokenKeepStr()
    {
        return substr(date('thLzmhyytLzm'), 0, 10);
    }

    public static function isValidToken($token): bool
    {
        return substr($token, 11, 10) === self::getTokenKeepStr();
    }
}
