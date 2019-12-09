<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Form\User\RegisterType;
use MsgPhp\User\Command\CreateUser;
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
        MessageBusInterface $bus,
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response {


        $form = $formFactory->createNamed('', RegisterType::class);

        if($request->isMethod(Request::METHOD_GET))
        {
//            return new Response($twig->render('user/register.html.twig', [
//                'form' => $form->createView(),
//            ]));

            $_token = $form->createView()->children['_token']->vars['data'];
             return $this->json(['_token'=> $_token]);
        }

        $data = json_decode($request->getContent(), true);
        if($data)
            foreach ($data as $key => $value) {
                if($key === 'password')
                    $value = ['plain' => $value];

                $request->request->add([$key => $value]);
            }

        $form->handleRequest($request);

        //$form->submit($data);
//        $_token = null;

        if(isset($data['_token']))
        {
            $_token = new CsrfToken('App\Form\User\RegisterType', $data['_token']);
        }

//        if(!$csrfTokenManager->isTokenValid($_token)){
//            return $this->json(['_error' => "Invalid CSRF Token"], 403);
//        }

        if ($form->isSubmitted() && $form->isValid()) {
            $bus->dispatch(new CreateUser($form->getData()));
            $flashBag->add('success', 'You\'re successfully registered.');

            return $this->json(['message'=> 'You\'re successfully registered. We\'ve send you an email about it']);
        }
        else
        {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                if(is_array($error->getMessageParameters()) && count($error->getMessageParameters()) > 0)
                {
//                    dump($error->getMessageParameters());
                    $value = array_values($error->getMessageParameters())[0];
                    $errors[] = $error->getMessageTemplate().': '.$value;
                }

            }
            return $this->json(['message' => 'The form contains errors', 'errors' => $errors]);
        }
    }
}
