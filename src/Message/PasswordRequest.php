<?php


namespace App\Message;

use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

class PasswordRequest
{
    private $userId;
    public ResetPasswordToken $token;

    /**
     * PasswordRequest constructor.
     * @param $userId
     * @param ResetPasswordToken $token
     */
    public function __construct($userId, ResetPasswordToken $token)
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
     * @return ResetPasswordToken
     */
    public function getToken(): ResetPasswordToken
    {
        return $this->token;
    }


}
