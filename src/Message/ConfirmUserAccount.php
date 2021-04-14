<?php


namespace App\Message;

class ConfirmUserAccount
{
    public int $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }


}
