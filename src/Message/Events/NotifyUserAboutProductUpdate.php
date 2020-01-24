<?php


namespace App\Message\Events;


use App\Entity\User;

class NotifyUserAboutProductUpdate
{
    private $productId;


    public function __construct($userId, $productId)
    {
        $this->userId = $userId;

        $this->productId = $productId;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->productId;
    }




}