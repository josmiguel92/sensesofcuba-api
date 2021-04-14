<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait CanBeEnabled
{
    /** @var bool */
    /**
     * @ORM\Column(type="boolean")
     */
    private bool $enabled = false;

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }
}
