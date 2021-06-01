<?php


namespace App\ORM\Fields;

use Doctrine\ORM\Mapping as ORM;

trait PriorityTrait
{

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $priority;


    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }
}
