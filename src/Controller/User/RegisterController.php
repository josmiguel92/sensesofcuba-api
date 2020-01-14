<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Form\User\RegisterType;
use App\Message\Events\UserRegistered;
use MsgPhp\User\Command\CreateUser;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Environment;

/**
 * @Route("/api/register", name="register", methods={"POST", "GET"})
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

        if($request->isMethod(Request::METHOD_GET))
        {
            return $this->json(['_token'=>self::getToken()]);
        }

        $data = json_decode($request->getContent(), true);

        $form = $formFactory->create(RegisterType::class);
        $dataNormalized = [];
        if($data)
            foreach ($data as $key => $value) {
                if($key === 'password')
                    $value = ['plain' => $value];
                $dataNormalized[$key] = $value;
            }
        
        if(!self::isValidToken($data['_token'])){
            return $this->json(['_error' => "Invalid CSRF Token"], 400);
        }

        $form->submit($dataNormalized);

        if ($form->isSubmitted() && $form->isValid()) {
            $bus->dispatch(new CreateUser($form->getData()));
            $flashBag->add('success', 'You\'re successfully registered.');

            return $this->json(['message'=> 'You\'re successfully registered. We\'ve send you an email about it']);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            if(is_array($error->getMessageParameters()) && count($error->getMessageParameters()) > 0)
            {
//                    dump($error->getMessageParameters());
                $value = array_values($error->getMessageParameters())[0];
                $errors[] = $error->getMessageTemplate().': '.$value;
            }

        }
        return $this->json(['message' => 'The form contains errors', 'errors' => $errors], Response::HTTP_BAD_REQUEST);
    }

    public static function getToken(): string
    {
        return substr(uniqid("t", true), 0, 10).'-'.self::getTokenKeepStr();
    }

    private static function getTokenKeepStr()
    {
        return substr(date('thLzmhyytLzm'), 0, 10);
    }

    public static function isValidToken($token):bool
    {
        return substr($token, 11, 10) === self::getTokenKeepStr();
    }
}
