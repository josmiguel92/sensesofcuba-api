<?php


namespace App\ORM\Fields\Timestampable;

/**
 * Timestampable trait.
 *
 * Should be used inside entity, that needs to be timestamped.
 */
trait TimestampableProperties
{
    protected $createdAt;

    protected $updatedAt;
}
