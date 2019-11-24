<?php

declare(strict_types=1);

namespace App\Http;

final class RespondTemplate extends Respond
{
    public $name;
    public $context;

    public function __construct(string $name, array $context = [])
    {
        $this->name = $name;
        $this->context = $context;
    }
}
