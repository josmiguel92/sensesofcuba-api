<?php

namespace App\Entity\Traits;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

trait CreatedAtField
{
    /** @var DateTimeInterface */
    /**
     * @ORM\Column(type="datetime", length=180)
     */
    private DateTimeInterface $createdAt;

    /** @var DateTimeInterface */
    /**
     * @ORM\Column(type="datetime", length=180)
     */
    private DateTimeInterface $updatedAt;


    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @return DateTimeInterface
     */
    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }


}
