<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SocImageRepository")
 * @Vich\Uploadable()
 */
class SocImage
{
    use ORMBehaviors\Timestampable\Timestampable;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
//
//    /**
//     * @ORM\Column(type="string", length=180)
//     */
//    private $title;
//

    /**
    * @Vich\UploadableField(mapping="images", fileNameProperty="imageName", size="imageSize")
    */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $imageName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $imageSize;

    public function getId(): ?int
    {
        return $this->id;
    }
//
//    public function getTitle(): ?string
//    {
//        return $this->title;
//    }
//
//    public function setTitle(string $title): self
//    {
//        $this->title = $title;
//
//        return $this;
//    }


    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(string $imageName = null): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function setImageSize(int $imageSize = null): self
    {
        $this->imageSize = $imageSize;

        return $this;
    }

    /**
     * @return File
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param File|UploadedFile $file
     * @return SocImage
     * @throws \Exception
     */
    public function setImageFile(?File $file = null): self
    {
        $this->imageFile = $file;
        if(null !== $file) {
            $this->updatedAt = new \DateTimeImmutable();
        }
        return $this;
    }

    public function __toString()
    {
        return $this->imageName;
    }


}
