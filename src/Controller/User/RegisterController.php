<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Form\User\RegisterType;
use App\Message\CreateUser;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
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
            try {
                $bus->dispatch(new CreateUser($form->getData()));
            } catch (HandlerFailedException $exception) {
                $message = 'An error occurred. ';
                foreach ($exception->getNestedExceptions() as $e) {
                    if ($e instanceof UniqueConstraintViolationException) {
                        $message .= 'The email provided is already registered.';
                        break;
                    }
                }
                return $this->json(['message' => $message], status: 403);
            }
            return $this->json(['message' => 'You\'re successfully registered. We\'ve send you an email about it']);
        }

        $token = $form->createView()->children['_token']->vars['value'] ?? null;
        return $this->json(['_token' => $token]);
    }

    public static function getToken(): string
    {
        return substr(uniqid("t", true), 0, 10) . '-' . self::getTokenKeepStr();
    }

    /**
     * @return string
     * a string depending on the date for time-validation the random tokens
     */
    private static function getTokenKeepStr(): string
    {
        return substr(date('thLzmhyytLzm'), 0, 10);
    }

    public static function isValidToken($token): bool
    {
        return substr($token, 11, 10) === self::getTokenKeepStr();
    }
}
