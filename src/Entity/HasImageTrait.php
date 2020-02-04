<?php


namespace App\Entity;


trait HasImageTrait
{

    public function hasImage(): bool
    {
        if ($this->getImage() instanceof SocImage && $this->getImage()->getImageName())
            return true;
        return false;
    }
}