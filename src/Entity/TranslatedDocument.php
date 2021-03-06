<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TranslatedDocumentRepository")
 */
class TranslatedDocument
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    private $lang = 'en';

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\SocFile", cascade={"persist", "remove"})
     */
    private $enFile;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\SocFile", cascade={"persist", "remove"})
     */
    private $esFile;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\SocFile", cascade={"persist", "remove"})
     */
    private $deFile;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEnFile(): ?SocFile
    {
        return $this->enFile;
    }

    public function setEnFile($enFile): self
    {
        $this->enFile = $this->createSocFile($enFile);

        return $this;
    }

    public function getEsFile(): ?SocFile
    {
        return $this->esFile;
    }

    public function setEsFile($esFile): self
    {
        $this->esFile = $this->createSocFile($esFile);

        return $this;
    }

    public function getDeFile(): ?SocFile
    {
        return $this->deFile;
    }

    public function setDeFile($deFile): self
    {
        $this->deFile = $this->createSocFile($deFile);

        return $this;
    }

    private function createSocFile($file):SocFile
    {
        if($file instanceof UploadedFile){
            $newSocFile = new SocFile();
            $newSocFile->setFile($file);
             return $newSocFile;
        }
       return $file;
    }

    public function getDocuments()
    {
        $docs = [];
        if($this->esFile && $this->esFile->getFileName()) {
            $docs[] = ['lang' => 'Spanish', 'file' => $this->esFile];
        }
        if($this->enFile && $this->enFile->getFileName()) {
            $docs[] = ['lang' => 'English', 'file' => $this->enFile];
        }
        if($this->deFile && $this->deFile->getFileName()) {
            $docs[] = ['lang' => 'German', 'file' => $this->deFile];
        }

        return $docs;


    }


    public function getTranslation($lang): ?SocFile
    {
        if(!in_array($lang, ['en', 'es', 'de'])) {
            return null;
        }

        switch ($lang)
        {
            case 'es' : return $this->getEsFile();
            case 'de' : return $this->getDeFile();
            case 'en' : return $this->getEnFile();
        }
    }

    /**
     * @return string
     */
    public function translate($lang, $fallbackEnglish = false): ?SocFile
    {
        if(in_array($lang, ['en', 'es', 'de'])) {
            $this->lang = $lang;
        }

        $translation = $this->getTranslation($lang);

        if($fallbackEnglish && $translation === null) {
            return $this->getEnFile();
        }

        return $translation;
    }

    public function __toString() {
        return "{". $this->id ."}";
    }
}
