<?php


namespace App\Message\Events;


use App\Entity\User;

class ProductUpdated
{
    private $productId;

    /**
     * @var string
     */
    private $changes;
    /**
     * @var false
     */
    private $sendNotification;

    /**
     * UserAccountConfirmed constructor.
     * @param $productId
     * @param string|null $changes
     */
    public function __construct($productId, $changes = null, $sendNotification = false)
    {
        $this->productId = $productId;
        $this->changes = $changes;
        $this->sendNotification = $sendNotification;
    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return string|null
     */
    public function getChanges(): ?string
    {
        return $this->changes;
    }

    /**
     * @return string
     */
    public function getChangesStr(): ?string
    {
        return $this->changes;
    }

    /**
     * @return false
     */
    public function getSendNotification()
    {
        return $this->sendNotification;
    }







}
