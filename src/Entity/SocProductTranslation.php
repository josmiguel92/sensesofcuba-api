<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

/**
 * @ORM\Entity()
 */
class SocProductTranslation implements TranslationInterface
{
    use TranslationTrait;


    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id;

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
     * @return SocProductTranslation
     */
    public function setName($name): SocProductTranslation
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
     * @return SocProductTranslation
     */
    public function setDescription($description): SocProductTranslation
    {
        $this->description = $description;
        return $this;
    }

    public function __toString(): string
    {
        return "[" . $this->locale . "] " . $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
