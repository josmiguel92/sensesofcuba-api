<?php


namespace App\Message\Events;


class PasswordReset
{
    private $email;

    /**
     * PasswordRequest constructor.
     * @param $email
     */
    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }





}