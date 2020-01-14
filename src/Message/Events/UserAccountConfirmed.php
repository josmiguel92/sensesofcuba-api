<?php


namespace App\Message\Events;


use App\Entity\User;

class UserAccountConfirmed
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