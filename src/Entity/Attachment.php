<?php

namespace App\Entity;

use App\ORM\Fields\MetadataTrait;
use App\ORM\Fields\TimestampableTrait;
use App\ORM\Interfaces\MetadataFieldInterface;
use App\ORM\Interfaces\TranslatableMetadataInterface;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AttachmentRepository")
 * @Vich\Uploadable()
 */
class Attachment implements MetadataFieldInterface
{
    use TimestampableTrait;
    use MetadataTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
    * @Vich\UploadableField(mapping="files", fileNameProperty="fileName")
    */
    private ?File $file = null;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private ?string $fileName = null;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="attachmentCollection")
     */
    private Product $product;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->fileName ?? null;
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
        if (isset($this->file)) {
            return $this->file;
        } else {
            return null;
        }
    }

    /**
     * @param File|null $file
     * @return Attachment
     */
    public function setFile(?File $file = null): self
    {
        $this->file = $file;
        if (null !== $file) {
            $this->updatedAt = new DateTimeImmutable();
        }
        return $this;
    }

    public function isEmpty(): bool
    {
        if (($this->fileName ?? null) !== null) {
            return false;
        }
        return true;
    }

    public function __toString(): ?string
    {
        return $this->fileName ?? null;
    }

    public static function getMetadataFieldTypeList(): array
    {
        return [
            //check if is simple language attachment. It will be used on every language as it.
            'multilingual' => CheckboxType::class,
            'lang' => TextType::class
        ];
    }

    /**
     * @return mixed
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param mixed $product
     * @return Attachment
     */
    public function setProduct(Product $product): Attachment
    {
        $this->product = $product;
        return $this;
    }



}
