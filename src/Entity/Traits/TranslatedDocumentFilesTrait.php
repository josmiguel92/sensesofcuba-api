<?php

namespace App\Entity\Traits;

use App\Entity\TranslatedDocument;

trait TranslatedDocumentFilesTrait
{

    public function hasEnglishFile(): bool
    {
        return ($this->hasDocumentObjects() && $this->translatedDocument->getEnFile() !== null);
    }

    public function hasSpanishFile(): bool
    {
        return ($this->hasDocumentObjects() && $this->translatedDocument->getEsFile() !== null);
    }
    public function hasGermanFile(): bool
    {
        return ($this->hasDocumentObjects() && $this->translatedDocument->getDeFile() !== null);
    }

    private function hasDocumentObjects(): bool
    {
        if (isset($this->translatedDocument) && $this->translatedDocument instanceof TranslatedDocument) {
            return true;
        }
        return false;
    }


    public function getEnglishFileLink(): ?string
    {

        if ($this->hasEnglishFile()) {
            return $this->translatedDocument->getEnFile()->getFileName();
        }
        return null;
    }

    public function getSpanishFileLink(): ?string
    {
        if ($this->hasSpanishFile()) {
            return $this->translatedDocument->getEsFile()->getFileName();
        }
        return null;
    }

    public function getGermanFileLink(): ?string
    {
        if ($this->hasGermanFile()) {
            return $this->translatedDocument->getDeFile()->getFileName();
        }
        return null;
    }
}
