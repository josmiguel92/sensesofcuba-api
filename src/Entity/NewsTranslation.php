<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity()
 */
class NewsTranslation
{
     use ORMBehaviors\Translatable\Translation;

     /**
     * @ORM\Column(type="string", length=180)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param $title
     * @return NewsTranslation
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return NewsTranslation
     */
    public function setDescription($description): NewsTranslation
    {
        $this->description = $description;
        return $this;
    }

    public function __toString()
    {
        return "[" . $this->locale . "] " . $this->title;
    }
}
