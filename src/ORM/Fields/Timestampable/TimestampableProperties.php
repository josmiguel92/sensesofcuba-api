<?php


namespace App\ORM\Fields\Timestampable;

use Doctrine\ORM\Mapping as ORM;

/**
 * Timestampable trait.
 *
 * Should be used inside entity, that needs to be timestamped.
 */
trait TimestampableProperties
{
    /**
     * @ORM\Column(type="date_immutable")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="date_immutable")
     */
    protected $updatedAt;
}
