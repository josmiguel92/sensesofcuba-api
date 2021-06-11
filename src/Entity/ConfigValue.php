<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\ORM\Fields\ActiveFieldTrait;
use App\ORM\Fields\MachineNameTrait;
use App\ORM\Fields\MetadataTrait;
use App\ORM\Interfaces\MachineNameInterface;
use App\ORM\Interfaces\MetadataFieldInterface;
use App\Repository\ConfigValueRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=ConfigValueRepository::class)
 */
class ConfigValue implements MachineNameInterface, MetadataFieldInterface
{
    use MachineNameTrait;
    use MetadataTrait;
    use ActiveFieldTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $value;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getNameFieldValue(): ?string
    {
        return $this->name;
    }

    public static function getMetadataFieldTypeList(): array
    {
        return ['comment' => TextareaType::class];
    }
}
