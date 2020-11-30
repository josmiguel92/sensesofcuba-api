<?php

namespace App\Entity\Traits;

use DateTime;

trait CreatedOrUpdatedDatePropertyTrait
{

    public function getCreatedOrUpdatedDate(): ?DateTime
    {
        if (isset($this->updatedAt) && isset($this->createdAt)) {
            if ($this->getUpdatedAt() !== null) {
                return $this->getUpdatedAt();
            } else {
                return $this->getCreatedAt();
            }
        }
        return null;
    }
}
