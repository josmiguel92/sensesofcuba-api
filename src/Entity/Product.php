<?php
// api/src/Entity/Product.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\User;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
// The class name will be used to name exposed resources
class Product
{
    use ORMBehaviors\Timestampable\Timestampable;

    private $tempFile;
    private $tempImage;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $title A Title property - this description will be available in the API documentation too.
     *
     * @ORM\Column
     * @Assert\NotBlank
     */
    public $title;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    public $imagePath;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    public $filePath;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Product
     */
    public function setTitle(string $title): Product
    {
        $this->title = $title;
        return $this;
    }



    
    public function getId(): ?int
    {
        return $this->id;
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


    private function getFileAbsolutePath()
    {
        return null === $this->filePath
            ? null
            : $this->getImageUploadRootDir().'/'.$this->filePath;
    }

    public function getFileWebPath()
    {
        return null === $this->filePath
            ? null
            : $this->getFileUploadDir().'/'.$this->filePath;
    }

    private function getFileUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../public/'.$this->getFileUploadDir();
    }

    private function getFileUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/files';
    }

    /**
     * Assert\File(maxSize="20000000")
     */
    private $file;

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->filePath)) {
            // store the old name to delete after the update
            $this->tempFile = $this->filePath;
            $this->filePath = null;
        } else {
            $this->filePath = 'initial';
        }
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    /**
     * @Assert\File(maxSize="20000000")
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductCategory", inversedBy="products")
     */
    private $category;

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
        if (null === $this->getFile() && null === $this->getImage()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to

        if($this->getFile()){
            $this->getFile()->move(
                $this->getFileUploadRootDir(),
                $this->filePath
            );

            // check if we have an old image
            if (isset($this->tempFile)) {
                // delete the old image
                unlink($this->getFileUploadRootDir().'/'.$this->tempFile);
                // clear the temp image path
                $this->tempFile = null;
            }
            $this->file = null;
        }

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
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = uniqid(mt_rand());
            $this->filePath = $filename.'.'.$this->getFile()->guessExtension();
        }

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

        $file = $this->getFileAbsolutePath();
        if ($file) {
            @unlink($file);
        }

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

    public function __toString()
    {
        return $this->title;
    }


}