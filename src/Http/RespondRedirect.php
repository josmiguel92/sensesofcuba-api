<?php

declare(strict_types=1);

namespace App\Http;

use Symfony\Component\HttpFoundation\Response;

final class RespondRedirect extends Respond
{
    public $url;

    public function __construct(string $url)
    {
        $this->status = Response::HTTP_FOUND;
        $this->url = $url;
    }
}
