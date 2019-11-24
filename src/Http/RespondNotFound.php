<?php

declare(strict_types=1);

namespace App\Http;

use Symfony\Component\HttpFoundation\Response;

class RespondNotFound extends Respond
{
    public function __construct()
    {
        $this->status = Response::HTTP_NOT_FOUND;
    }
}
