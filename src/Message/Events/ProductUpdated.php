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
     * UserAccountConfirmed constructor.
     * @param $productId
     * @param string $changes
     */
    public function __construct($productId, $changes)
    {
        $this->productId = $productId;
        $this->changes = $changes;
    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return array
     */
    public function getChanges(): array
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






}
