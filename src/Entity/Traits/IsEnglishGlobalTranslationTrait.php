<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait IsEnglishGlobalTranslationTrait
{

    /** @var bool */
    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isEnglishGlobalTranslation = false;

    /**
     * @return bool
     */
    public function isEnglishGlobalTranslation(): bool
    {
        return $this->isEnglishGlobalTranslation;
    }

    /**
     * @param bool $isEnglishGlobalTranslation
     * @return IsEnglishGlobalTranslationTrait
     */
    public function setIsEnglishGlobalTranslation(bool $isEnglishGlobalTranslation): IsEnglishGlobalTranslationTrait
    {
        $this->isEnglishGlobalTranslation = $isEnglishGlobalTranslation;
        return $this;
    }
}
