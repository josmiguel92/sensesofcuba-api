<?php

declare(strict_types=1);

namespace App\Http;

use Symfony\Component\HttpFoundation\Response;

class RespondEmpty extends Respond
{
    public function __construct()
    {
        $this->status = Response::HTTP_NO_CONTENT;
    }
}
