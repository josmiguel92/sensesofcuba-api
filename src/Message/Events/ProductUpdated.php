<?php


namespace App\Message\Events;


use App\Entity\User;

class ProductUpdated
{
    private $productId;

    /**
     * UserAccountConfirmed constructor.
     * @param $productId
     */
    public function __construct($productId)
    {
        $this->productId = $productId;
    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->productId;
    }





}