<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

/**
 * @ORM\Entity()
 */
class NewsTranslation implements TranslationInterface
{
    use TranslationTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id;

    public function getId(): ?int
    {
        //It only should return null for translations un-persisted
        return isset($this->id) ? $this->id : null;
    }


    /**
     * @ORM\Column(type="string", length=180)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description = null;

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
    public function setTitle($title): NewsTranslation
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

    public function __toString(): string
    {
        return "[" . $this->locale . "] " . $this->title;
    }
}
