<?php


namespace App\Message;

class PasswordRequest
{
    private $userId;
    public ?string $token;

    /**
     * PasswordRequest constructor.
     * @param $userId
     * @param string|null $token
     */
    public function __construct($userId, string $token = null)
    {
        $this->userId = $userId;
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }


}
