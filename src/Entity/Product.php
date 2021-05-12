<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\ORM\Fields\ActiveFieldTrait;
use App\ORM\Fields\MetadataTrait;
use App\ORM\Fields\PriorityTrait;
use App\ORM\Fields\TimestampableTrait;
use App\ORM\Fields\UniqueIdTrait;
use App\ORM\Interfaces\TranslatableMetadataInterface;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Product implements TranslatableMetadataInterface
{
    use TimestampableTrait;
    use MetadataTrait;
    use ActiveFieldTrait;
    use PriorityTrait;
    use UniqueIdTrait;

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
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    public function __construct()
    {
        $this->products = new ArrayCollection();
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

    public function getLangList(): array
    {
        return ['en', 'de'];
    }

    public function getTranslatableMetadataNames(): array
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
}
