<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait IsEnglishGlobalTranslationTrait
{

    /** @var bool */
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isEnglishGlobalTranslation = true;

    /**
     * @return bool
     */
    public function isEnglishGlobalTranslation(): bool
    {
        return $this->isEnglishGlobalTranslation ?? false;
    }

    /**
     * @param bool $isEnglishGlobalTranslation
     */
    public function setIsEnglishGlobalTranslation(?bool $isEnglishGlobalTranslation = false)
    {
        $this->isEnglishGlobalTranslation = $isEnglishGlobalTranslation;
        return $this;
    }
}
