<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SocFileRepository")
 * @Vich\Uploadable()
 */
class SocFile
{
    use ORMBehaviors\Timestampable\Timestampable;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
    * @Vich\UploadableField(mapping="files", fileNameProperty="fileName", size="fileSize")
    */
    private $file;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $fileName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $fileSize;

    
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

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    public function setFileSize(int $fileSize = null): self
    {
        $this->fileSize = $fileSize;

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
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @return SocFile
     * @throws \Exception
     */
    public function setFile(?File $file = null): self
    {
        $this->file = $file;
        if(null !== $file)
            $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function __toString()
    {
        return $this->fileName;
    }


}
