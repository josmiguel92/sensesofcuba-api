<?php


namespace App\Message\Events;

class UserRegistered
{
    private int $userId;

    /**
     * UserAccountConfirmed constructor.
     * @param $userId
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
