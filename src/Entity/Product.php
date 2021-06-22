<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\ORM\Fields\ActiveFieldTrait;
use App\ORM\Fields\MetadataTrait;
use App\ORM\Fields\PriorityTrait;
use App\ORM\Fields\TimestampableTrait;
use App\ORM\Fields\UniqueIdTrait;
use App\ORM\Interfaces\MetadataFieldInterface;
use App\ORM\Interfaces\TranslatableMetadataInterface;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Product implements MetadataFieldInterface, TranslatableMetadataInterface
{
    use TimestampableTrait;
    use MetadataTrait;
    use ActiveFieldTrait;
    use PriorityTrait;
    use UniqueIdTrait;

    public const ENABLED_LANGUAGES = ['en', 'de'];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="products")
     */
    private ?Product $parent;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="parent")
     */
    private $products;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name;

    /**
     * @ORM\ManyToOne(targetEntity=ProductCategory::class, inversedBy="products")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Attachment::class, mappedBy="product", cascade={"persist"})
     */
    private $attachmentCollection;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->attachmentCollection = new ArrayCollection();
        $this->updateTimestamps();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(self $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setParent($this);
        }

        return $this;
    }

    public function removeProduct(self $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getParent() === $this) {
                $product->setParent(null);
            }
        }

        return $this;
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

    public static function getLangList(): array
    {
        return self::ENABLED_LANGUAGES;
    }


    public static function getMetadataFieldTypeList(): array
    {
        return ['useDefaultLanguage' => CheckboxType::class];
    }

    public static function getTranslatableMetadataNames(): array
    {
        return [
            'name',
            'description',
        ];
    }

    public function getCategory(): ?ProductCategory
    {
        return $this->category;
    }

    public function setCategory(?ProductCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Attachment[]
     */
    public function getAttachmentCollection(): Collection
    {
        return $this->attachmentCollection;
    }


    public function addAttachmentCollection(Attachment $attachment): self
    {
        if (!$this->attachmentCollection->contains($attachment)) {
            $this->attachmentCollection[] = $attachment;
            $attachment->setProduct($this);
        }

        return $this;
    }

    public function removeAttachmentCollection(Attachment $attachment): self
    {
        if ($this->attachmentCollection->contains($attachment)) {
            $this->attachmentCollection->removeElement($attachment);
            // set the owning side to null (unless already changed)
            if ($attachment->getProduct() === $this) {
                $attachment->setProduct(null);
            }
        }

        return $this;
    }

}
