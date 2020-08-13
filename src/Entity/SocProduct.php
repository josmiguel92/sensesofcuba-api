<?php

namespace App\Entity;

use App\Entity\Traits\IsEnglishGlobalTranslationTrait;
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
    use ORMBehaviors\Translatable\Translatable;
    use ORMBehaviors\Timestampable\Timestampable;
    use CanBeEnabled;
    use HasImageTrait;
    use IsEnglishGlobalTranslationTrait;

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

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\TranslatedDocument", cascade={"persist", "remove"})
     */
    private $translatedDocument;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="subscribedProducts")
     */
    private $subscribedUsers;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $importance;

    public function __construct()
    {
        $this->socProducts = new ArrayCollection();
        $this->subscribedUsers = new ArrayCollection();
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
        if($this->parent && $this->getReferenceName() )

            return  $this->parent->getReferenceName(). ' / ' . $this->getReferenceName();

        return $this->getReferenceName() ?? 'empty';
    }

    public function setEnabled(bool $enabled):void
    {
        $this->enabled = $enabled;
    }

    public function getTranslatedDocument(): ?TranslatedDocument
    {
        return $this->translatedDocument;
    }

    public function setTranslatedDocument(?TranslatedDocument $translatedDocument): self
    {
        $this->translatedDocument = $translatedDocument;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getSubscribedUsers(): Collection
    {
        return $this->subscribedUsers;
    }

    public function addSubscribedUser(User $subscribedUser): self
    {
        if (!$this->subscribedUsers->contains($subscribedUser)) {
            $this->subscribedUsers[] = $subscribedUser;
            $subscribedUser->addSubscribedProduct($this);
        }

        return $this;
    }

    public function removeSubscribedUser(User $subscribedUser): self
    {
        if ($this->subscribedUsers->contains($subscribedUser)) {
            $this->subscribedUsers->removeElement($subscribedUser);
            $subscribedUser->removeSubscribedProduct($this);
        }

        return $this;
    }

    public function getImportance(): ?int
    {
        return $this->importance;
    }

    public function setImportance(?int $importance): self
    {
        $this->importance = $importance;

        return $this;
    }

    public function isAvailableForLang(string $lang): bool
    {
        if($this->translate($lang, $this->isEnglishGlobalTranslation())) {
            return true;
        }

        if($this->getTranslatedDocument()
            && $this->getTranslatedDocument()->translate($lang, $this->isEnglishGlobalTranslation)) {
            return true;
        }

        return false;
    }

    public function getTranslatedDocumentFilePathByLang($lang): ?string
    {
        if($this->getTranslatedDocument()
        && $this->getTranslatedDocument()->translate($lang, $this->isEnglishGlobalTranslation)
        && $filename = $this->getTranslatedDocument()->translate($lang, $this->isEnglishGlobalTranslation)->getFileName())
        {
            return 'uploads/files/' . $filename;
        }
        return null;
    }

    public function getTranslatedNameOrReference($lang): string
    {
        if($this->translate($lang) !== null && $this->translate($lang)->getName()) {
            return $this->translate($lang)->getName();
        }
        else {
            return $this->getReferenceName();
        }
    }

    public function getTranslatedDescOrNull($lang): ?string
    {
        if($this->translate($lang) !== null && $this->translate($lang)->getDescription())
            return $this->translate($lang)->getDescription();
        else return null;
    }
}
