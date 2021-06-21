<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\DataTransformer\ImageThumbnailer;
use App\ORM\Fields\ActiveFieldTrait;
use App\ORM\Fields\MetadataTrait;
use App\ORM\Fields\PriorityTrait;
use App\ORM\Fields\TimestampableTrait;
use App\ORM\Interfaces\MetadataFieldInterface;
use App\ORM\Interfaces\TranslatableMetadataInterface;
use App\Repository\ProductCategoryRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=ProductCategoryRepository::class)
 * @Vich\Uploadable()
 * @ORM\HasLifecycleCallbacks()
 */
class ProductCategory implements MetadataFieldInterface, TranslatableMetadataInterface
{
    use MetadataTrait;
    use TimestampableTrait;
    use PriorityTrait;
    use ActiveFieldTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="category", orphanRemoval=true)
     */
    private $products;

    /**
     * @Vich\UploadableField(mapping="images", fileNameProperty="imageName")
     */
    private ?File $imageFile;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private ?string $imageName = null;


    public function getImageName(): ?string
    {
        return $this->imageName ?? null;
    }

    public function setImageName(string $imageName = null): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * @return File
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile ?? null;
    }

    /**
     * @param File|null $file
     * @return SocImage
     */
    public function setImageFile(?File $file = null): self
    {
        $this->imageFile = $file;
        if (null !== $file) {
            $this->updatedAt = new DateTimeImmutable();
        }

        return $this;
    }


    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
            }
        }

        return $this;
    }


    public function getThumbnailPath(): ?string
    {
        return $this->imageName ? 'thumbs/' . $this->imageName : null;
    }

    public function getThumbnailURL(): ?string
    {
        return $this->imageName ? 'uploads/images/' . $this->getThumbnailPath() : null;
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate ()
     */
    public function createCustomThumbnail(): ?bool
    {
        if (isset($this->imageName)) {
            $uploadDir = __DIR__ . '/../../public/uploads/images/';
            $thumbnail = new ImageThumbnailer($uploadDir, $this->imageName);
            return $thumbnail->makeThumb();
        }
        return false;
    }

    public static function getMetadataFieldTypeList(): array
    {
        return [
            'useDefaultLanguage' => CheckboxType::class
        ];
    }

    public static function getTranslatableMetadataNames(): array
    {
        return [
            'title' => TextType::class,
            'description' => TextareaType::class,
        ];
    }

    public static function getLangList(): array
    {
        return Product::ENABLED_LANGUAGES;
    }
}
