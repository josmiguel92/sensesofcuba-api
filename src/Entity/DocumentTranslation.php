<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity()
 */
class DocumentTranslation
{
     use ORMBehaviors\Translatable\Translation;

     /**
     * @ORM\Column(type="string", length=180)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return DocumentTranslation
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * @return DocumentTranslation
     */
    public function setDescription($description): DocumentTranslation
    {
        $this->description = $description;
        return $this;
    }

    public function __toString()
    {
        return "[". $this->locale . "] ". $this->name;
    }

}