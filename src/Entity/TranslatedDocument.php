<?php

namespace App\Entity;

use App\Entity\Traits\IsEnglishGlobalTranslationTrait;
use App\Entity\Traits\ShowAndHideForUserGroups;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TranslatedDocumentRepository")
 */
class TranslatedDocument
{
    use ShowAndHideForUserGroups;
    use IsEnglishGlobalTranslationTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=SocProduct::class, inversedBy="documentCollection")
     */
    private $socProduct;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\SocFile", cascade={"persist", "remove"})
     */
    private ?SocFile $enFile;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\SocFile", cascade={"persist", "remove"})
     */
    private ?SocFile $esFile;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\SocFile", cascade={"persist", "remove"})
     */
    private ?SocFile $deFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEnFile(): ?SocFile
    {
        return $this->enFile ?? null;
    }

    public function setEnFile($enFile): self
    {
        $this->enFile = $this->createSocFile($enFile);

        return $this;
    }

    public function getEsFile(): ?SocFile
    {
        return $this->esFile ?? null;
    }

    public function setEsFile($esFile): self
    {
        $this->esFile = $this->createSocFile($esFile);

        return $this;
    }

    public function getDeFile(): ?SocFile
    {
        return $this->deFile ?? null;
    }

    public function setDeFile($deFile): self
    {
        $this->deFile = $this->createSocFile($deFile);

        return $this;
    }

    private function createSocFile($file): SocFile
    {
        if ($file instanceof UploadedFile) {
            $newSocFile = new SocFile();
            try {
                $newSocFile->setFile($file);
            } catch (Exception $e) {
            }
            return $newSocFile;
        }
        return $file;
    }

    public function getDocuments(): array
    {
        $docs = [];
        if ($this->esFile && $this->esFile->getFileName()) {
            $docs[] = ['lang' => 'Spanish', 'file' => $this->esFile];
        }
        if ($this->enFile && $this->enFile->getFileName()) {
            $docs[] = ['lang' => 'English', 'file' => $this->enFile];
        }
        if ($this->deFile && $this->deFile->getFileName()) {
            $docs[] = ['lang' => 'German', 'file' => $this->deFile];
        }

        return $docs;
    }


    public function getTranslation($lang): ?SocFile
    {
        if (!in_array($lang, ['en', 'es', 'de'])) {
            return null;
        }

        switch ($lang) {
            case 'es':
                return $this->getEsFile();
            case 'de':
                return $this->getDeFile();
            case 'en':
                return $this->getEnFile();
        }
        return null;
    }

    /**
     * @param $lang
     * @param bool $fallbackEnglish
     * @return SocFile|null
     */
    public function translate($lang, $fallbackEnglish = false): ?SocFile
    {
        $translation = in_array($lang, ['en', 'es', 'de']) ? $this->getTranslation($lang) : null;

        if ($fallbackEnglish && $translation === null) {
            return $this->getEnFile();
        }

        return $translation;
    }


    public function getSocProduct(): ?SocProduct
    {
        return $this->socProduct;
    }

    public function setSocProduct(?SocProduct $socProduct): self
    {
        $this->socProduct = $socProduct;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf("{%s}", $this->id);
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function isEmpty(): bool
    {
        if ($this->getDeFile() && !$this->getDeFile()->isEmpty()) {
            return false;
        }
        if ($this->getEnFile() && !$this->getEnFile()->isEmpty()) {
            return false;
        }
//        if ($this->title !== null) {
//            return false;
//        }
        return true;
    }
}
