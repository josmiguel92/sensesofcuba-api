<?php

declare(strict_types=1);

namespace App\Http;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RespondRouteRedirect extends Respond
{
    public $name;
    public $parameters;
    public $referenceType;

    public function __construct(string $name, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $this->status = Response::HTTP_FOUND;
        $this->name = $name;
        $this->parameters = $parameters;
        $this->referenceType = $referenceType;
    }
}
