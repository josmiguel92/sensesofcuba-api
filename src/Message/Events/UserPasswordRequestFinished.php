<?php

namespace App\Message\Events;

use App\Entity\User;

class UserPasswordRequested
{
    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
