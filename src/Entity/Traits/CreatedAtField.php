<?php

namespace App\Entity\Traits;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 *  * @ORM\HasLifecycleCallbacks
 */
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


    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt ?? null;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt ?? null;
    }

    /**
     * Updates createdAt and updatedAt timestamps.
     */
    public function updateTimestamps(): void
    {
        // Create a datetime with microseconds
        $dateTime = \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)));
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));

        if (null === $this->getCreatedAt()) {
            $this->createdAt = $dateTime;
        }

        $this->updatedAt = $dateTime;
    }


}
