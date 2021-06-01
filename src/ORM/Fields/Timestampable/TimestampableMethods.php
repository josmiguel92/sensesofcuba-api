<?php

namespace App\ORM\Fields\Timestampable;

use \DateTime;
use \DateTimeZone;
use Doctrine\ORM\Mapping as ORM;

/**
 * Timestampable trait.
 *
 * Should be used inside entity, that needs to be timestamped.
 */

/**
 * Trait TimestampableMethods
 * @package App\ORM\Fields\Timestampable
 * @ORM\HasLifecycleCallbacks
 */
trait TimestampableMethods
{
    /**
     * Returns createdAt value.
     *
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        if (!isset($this->createdAt)) {
            $this->updateTimestamps();
        }
        return $this->createdAt;
    }

    /**
     * Returns updatedAt value.
     *
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        if (!isset($this->updatedAt)) {
            $this->updateTimestamps();
        }
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @param \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Updates createdAt and updatedAt timestamps.
     */
    public function updateTimestamps(): void
    {
        // Create a datetime with microseconds
        $dateTime = \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)));
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));

        if (null === $this->createdAt) {
            $this->createdAt = $dateTime;
        }

        $this->updatedAt = $dateTime;
    }
}
