<?php


namespace App\Message\Events;


class UserRegistered
{
   private $userId;

    /**
     * UserAccountConfirmed constructor.
     * @param $userId
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function getUserId()
    {
        return $this->userId;
    }


}