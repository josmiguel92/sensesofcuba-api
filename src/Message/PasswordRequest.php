<?php


namespace App\Message;


class PasswordRequest
{
    private $userId;

    /**
     * PasswordRequest constructor.
     * @param $userId
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }




}