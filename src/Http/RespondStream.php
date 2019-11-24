<?php

declare(strict_types=1);

namespace App\Http;

final class RespondStream extends Respond
{
    public $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }
}
