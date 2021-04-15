<?php

namespace App\Message;

use App\Entity\User;

class CreateUser
{
    private array $userRaw;

    /**
     * CreateUser constructor.
     * @param array $userRaw
     */
    public function __construct(array $userRaw)
    {
        $this->userRaw = $userRaw;
    }

    /**
     * @return array
     */
    public function getUserRaw(): array
    {
        return $this->userRaw;
    }


}
