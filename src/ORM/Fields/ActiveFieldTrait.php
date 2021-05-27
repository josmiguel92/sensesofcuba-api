<?php


namespace App\ORM\Fields;

use Doctrine\ORM\Mapping as ORM;

trait ActiveFieldTrait
{

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private bool $active;


    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active = true): self
    {
        $this->active = $active;

        return $this;
    }

    public function toggleActive(): self
    {
        $this->active = !$this->active;

        return $this;
    }
}
