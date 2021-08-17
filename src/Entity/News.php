<?php

namespace App\Entity;

use App\Entity\Traits\CreatedAtField;
use App\Entity\Traits\CreatedOrUpdatedDatePropertyTrait;
use App\Entity\Traits\IsEnglishGlobalTranslationTrait;
use App\Entity\Traits\TranslatedDocumentFilesTrait;
use App\Repository\NewsRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\CanBeEnabled;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * @ORM\Entity(repositoryClass=NewsRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class News implements TranslatableInterface
{
    use TranslatableTrait;
    use CreatedOrUpdatedDatePropertyTrait;
    use TranslatedDocumentFilesTrait;
    use CanBeEnabled;
    use IsEnglishGlobalTranslationTrait;
    use CreatedAtField;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private string $referenceName;


    /**
     * @ORM\OneToOne(targetEntity="App\Entity\TranslatedDocument", cascade={"persist", "remove"})
     */
    private ?TranslatedDocument $translatedDocument;

    public function getReferenceName(): string
    {
        return $this->referenceName ?: '';
    }

    public function setReferenceName(string $referenceName): News
    {
        $this->referenceName = $referenceName;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getReferenceName();
    }

    public function setEnabled(bool $enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getTranslatedDocument(): ?TranslatedDocument
    {
        return $this->translatedDocument;
    }

    public function setTranslatedDocument(?TranslatedDocument $translatedDocument): News
    {
        $this->translatedDocument = $translatedDocument;

        return $this;
    }

    /**
     * @ORM\PrePersist ()
     * @ORM\PreUpdate()
     */
    public function selfUpdateTimestamps()
    {
        $this->updateTimestamps();
    }
}
