<?php

namespace App\ORM\Fields;

use App\ORM\Fields\Timestampable\TimestampableMethods;
use App\ORM\Fields\Timestampable\TimestampableProperties;

/**
 * Timestampable trait.
 *
 * Should be used inside entity, that needs to be timestamped.
 */
trait TimestampableTrait
{
    use TimestampableProperties;
    use TimestampableMethods;
}
