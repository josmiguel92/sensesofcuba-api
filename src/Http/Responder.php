<?php

declare(strict_types=1);

namespace App\Http;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

final class Responder
{
    private $urlGenerator;
    private $twig;
    private $flashBag;

    public function __construct(UrlGeneratorInterface $urlGenerator, Environment $twig, FlashBagInterface $flashBag)
    {
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
        $this->flashBag = $flashBag;
    }

    public function respond(Respond $respond): Response
    {
        foreach ($respond->flashes as $type => $messages) {
            foreach ((array) $messages as $message) {
                $this->flashBag->add($type, $message);
            }
        }

        if ($respond instanceof RespondBadRequest) {
            throw new BadRequestHttpException(null, null, 0, $respond->headers);
        }

        if ($respond instanceof RespondNotFound) {
            throw new NotFoundHttpException(null, null, 0, $respond->headers);
        }

        if ($respond instanceof RespondTemplate) {
            return new Response($this->twig->render($respond->name, $respond->context), $respond->status, $respond->headers);
        }

        if ($respond instanceof RespondRouteRedirect) {
            return new RedirectResponse($this->urlGenerator->generate($respond->name, $respond->parameters, $respond->referenceType), $respond->status, $respond->headers);
        }

        if ($respond instanceof RespondRedirect) {
            return new RedirectResponse($respond->url, $respond->status, $respond->headers);
        }

        if ($respond instanceof RespondJson) {
            return new JsonResponse($respond->data, $respond->status, $respond->headers);
        }

        if ($respond instanceof RespondRawJson) {
            return JsonResponse::fromJsonString($respond->contents, $respond->status, $respond->headers);
        }

        if ($respond instanceof RespondFile) {
            return new BinaryFileResponse($respond->file, $respond->status, $respond->headers);
        }

        if ($respond instanceof RespondStream) {
            return new StreamedResponse($respond->callback, $respond->status, $respond->headers);
        }

        if ($respond instanceof RespondRaw) {
            return new Response($respond->contents, $respond->status, $respond->headers);
        }

        return new Response('', $respond->status, $respond->headers);
    }
}
