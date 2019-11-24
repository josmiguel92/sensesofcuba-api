<?php

declare(strict_types=1);

namespace App\Http;

final class RespondJson extends Respond
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
