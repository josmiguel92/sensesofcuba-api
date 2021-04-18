<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SocFileRepository")
 * @Vich\Uploadable()
 */
class SocFile implements TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
    * @Vich\UploadableField(mapping="files", fileNameProperty="fileName")
    */
    private ?File $file;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private ?string $fileName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $title;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName = null): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @return File
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * @param File|null $file
     * @return SocFile
     */
    public function setFile(?File $file = null): self
    {
        $this->file = $file;
        if (null !== $file) {
            $this->updatedAt = new DateTimeImmutable();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return SocFile
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }



    public function __toString(): ?string
    {
        return $this->fileName;
    }
}
