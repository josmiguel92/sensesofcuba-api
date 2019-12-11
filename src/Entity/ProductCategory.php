<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductCategoryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ProductCategory
{
    use ORMBehaviors\Timestampable\Timestampable;

    private $tempImage;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="category")
     */
    private $products;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductCategory", inversedBy="children")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductCategory", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\Column(type="string",length=180, nullable=true)
     */
    public $imagePath;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

     private function getImageAbsolutePath()
    {
        return null === $this->imagePath
            ? null
            : $this->getImageUploadRootDir().'/'.$this->imagePath;
    }

    public function getImageWebPath()
    {
        return null === $this->imagePath
            ? null
            : $this->getImageUploadDir().'/'.$this->imagePath;
    }

    private function getImageUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../public/'.$this->getImageUploadDir();
    }

    private function getImageUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/images';
    }


    /**
     * @Assert\File(maxSize="6000000")
     */
    private $image;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->parent ? $this->parent . " »» ". $this->title : $this->title;
//        return $this->title;
    }

    public function getParent(): ?self
    {;
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
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChildren(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChildren(self $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }



      /**
     * Sets file.
     *
     * @param UploadedFile|null $image
     */
    public function setImage(UploadedFile $image = null)
    {
        $this->image = $image;
        // check if we have an old image path
        if (isset($this->imagePath)) {
            // store the old name to delete after the update
            $this->temp = $this->imagePath;
            $this->imagePath = null;
        } else {
            $this->imagePath = 'initial';
        }
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getImage(): ?UploadedFile
    {
        return $this->image;
    }

     /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getImage()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to

        if($this->getImage()){
            $this->getImage()->move(
                $this->getImageUploadRootDir(),
                $this->imagePath
            );

            if (isset($this->tempImage)) {
                // delete the old image
                unlink($this->getImageUploadRootDir().'/'.$this->tempImage);
                // clear the temp image path
                $this->tempImage = null;
            }
            $this->image = null;
        }
    }

     /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {

        if (null !== $this->getImage()) {
            // do whatever you want to generate a unique name
            $filename = uniqid(mt_rand());
            $this->imagePath = $filename.'.'.$this->getImage()->guessExtension();
        }
    }

      /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        $file = $this->getImageAbsolutePath();
        if ($file) {
            @unlink($file);
        }

    }

}
