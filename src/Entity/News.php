<?php

namespace App\Entity;

use App\Entity\Traits\IsEnglishGlobalTranslationTrait;
use App\Repository\NewsRepository;
use Doctrine\ORM\Mapping as ORM;
use MsgPhp\Domain\Model\CanBeEnabled;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity(repositoryClass=NewsRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class News
{
    use ORMBehaviors\Translatable\Translatable;
    use ORMBehaviors\Timestampable\Timestampable;
    use CanBeEnabled;
    use IsEnglishGlobalTranslationTrait;

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


}
