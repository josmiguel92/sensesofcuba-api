<?php


namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait ShowAndHideForUserGroups
{


    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private ?array $showForGroups = [];

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private ?array $hideForGroups = [];


    public function getShowForGroups(): ?array
    {
        return $this->showForGroups;
    }

    public function setShowForGroups($showForGroups): self
    {
        foreach ($showForGroups as $item) {
            $str = (string) $item;
            $this->showForGroups[] = $str;
        }
        return $this;
    }

    public function getHideForGroups(): ?array
    {
        return $this->hideForGroups;
    }

    public function setHideForGroups($hideForGroups): self
    {
        foreach ($hideForGroups as $item) {
            $str = (string) $item;
            $this->hideForGroups[] = $str;
        }
        return $this;
    }
}
