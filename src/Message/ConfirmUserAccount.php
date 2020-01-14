<?php


namespace App\Message;


use MsgPhp\User\UserId;

class ConfirmUserAccount
{
    public $userId;

    public function __construct( $userId)
    {
        $this->userId = $userId;
    }
}