<?php
// api/src/Entity/Document.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use MsgPhp\Domain\Model\CanBeEnabled;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
// The class name will be used to name exposed resources
class Document
{

    use ORMBehaviors\Translatable\Translatable,
       ORMBehaviors\Timestampable\Timestampable,
        CanBeEnabled;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SocFile", cascade={"persist","remove"})
     */
    private $file;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SocImage", cascade={"persist", "remove"})
     */
    private $image;

     /**
     * @ORM\Column(type="string", length=180)
     */
    private $referenceName;


    public function getFile(): ?SocFile
    {
        return $this->file;
    }

    public function setFile(?SocFile $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getImage(): ?SocImage
    {
        return $this->image;
    }

    public function setImage(?SocImage $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getReferenceName(): ?string
    {
        return $this->referenceName;
    }

    public function setReferenceName(string $referenceName): self
    {
        $this->referenceName = $referenceName;

        return $this;
    }

    public function __toString()
    {
        return $this->getReferenceName() ? $this->getReferenceName() : 'empty';
    }

    public function setEnabled(bool $enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


}