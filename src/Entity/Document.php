<?php

// api/src/Entity/Document.php

namespace App\Entity;

use App\Entity\Traits\IsEnglishGlobalTranslationTrait;
use App\Entity\Traits\TranslatedDocumentFilesTrait;
use App\Entity\Traits\CreatedOrUpdatedDatePropertyTrait;
use Doctrine\ORM\Mapping as ORM;
use MsgPhp\Domain\Model\CanBeEnabled;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
// The class name will be used to name exposed resources
class Document
{
    use ORMBehaviors\Translatable\Translatable;
    use ORMBehaviors\Timestampable\Timestampable;
    use CreatedOrUpdatedDatePropertyTrait;
    use CanBeEnabled;
    use IsEnglishGlobalTranslationTrait;
    use TranslatedDocumentFilesTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

     /**
     * @ORM\Column(type="string", length=180)
     */
    private $referenceName;


    /**
     * @ORM\OneToOne(targetEntity="App\Entity\TranslatedDocument", cascade={"persist", "remove"})
     */
    private $translatedDocument;

       /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $importance;

    public function getReferenceName(): string
    {
        return $this->referenceName ?: '';
    }

    public function setReferenceName(string $referenceName)
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

    public function setTranslatedDocument(?TranslatedDocument $translatedDocument)
    {
        $this->translatedDocument = $translatedDocument;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImportance()
    {
        return $this->importance;
    }

    /**
     * @param mixed $importance
     * @return Document
     */
    public function setImportance($importance)
    {
        $this->importance = $importance;
        return $this;
    }
}
