<?php


namespace App\Message\Events;


class UserAccountEnabled
{
    private $email;
    private $name;

    /**
     * UserAccountEnabled constructor.
     * @param $email
     * @param $name
     */
    public function __construct($email, $name)
    {
        $this->email = $email;
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }



}