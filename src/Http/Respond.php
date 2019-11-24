<?php

declare(strict_types=1);

namespace App\Http;

abstract class Respond
{
    public $status = 200;
    public $headers = [];
    public $flashes = [];

    public function withStatus(int $status): self
    {
        $respond = clone $this;
        $respond->status = $status;

        return $respond;
    }

    public function withHeaders(array $headers): self
    {
        $respond = clone $this;
        $respond->headers = $headers;

        return $respond;
    }

    public function withFlashes(array $flashes): self
    {
        $respond = clone $this;
        $respond->flashes = $flashes;

        return $respond;
    }
}
