<?php

namespace App\Message\Events;

use App\Entity\User;

class UserPasswordRequestFinished
{
    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
