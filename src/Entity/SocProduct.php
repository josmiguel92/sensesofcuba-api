<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use MsgPhp\Domain\Model\CanBeEnabled;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SocProductRepository")
 */
class SocProduct
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
     * @ORM\ManyToOne(targetEntity="App\Entity\SocProduct", inversedBy="socProducts")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SocProduct", mappedBy="parent")
     */
    private $socProducts;

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

    public function __construct()
    {
        $this->socProducts = new ArrayCollection();
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
    public function getSocProducts(): Collection
    {
        return $this->socProducts;
    }

    public function addSocProduct(self $socProduct): self
    {
        if (!$this->socProducts->contains($socProduct)) {
            $this->socProducts[] = $socProduct;
            $socProduct->setParent($this);
        }

        return $this;
    }

    public function removeSocProduct(self $socProduct): self
    {
        if ($this->socProducts->contains($socProduct)) {
            $this->socProducts->removeElement($socProduct);
            // set the owning side to null (unless already changed)
            if ($socProduct->getParent() === $this) {
                $socProduct->setParent(null);
            }
        }

        return $this;
    }

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

}
