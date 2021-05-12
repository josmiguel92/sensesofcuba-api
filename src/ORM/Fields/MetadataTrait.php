<?php


namespace App\ORM\Fields;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

trait MetadataTrait
{
    /**
     * @var PropertyAccessor
     */
    private PropertyAccessor $propertyAccessor;

    protected function createPropertyAccessor(): void
    {
        if ($this->propertyAccessor instanceof PropertyAccessor === false) {
            $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
                ->disableExceptionOnInvalidPropertyPath()
                ->getPropertyAccessor();
        }
    }


    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private array $metadata = [];


    public function getMetadata(): ?array
    {
        if (is_array($this->metadata)) {
            return $this->metadata;
        }

        return [];
    }

    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function __get($name)
    {
        $this->createPropertyAccessor();

        if (property_exists($this, $name) === true) {
            return $this->propertyAccessor->getValue($this, $name);
        }

        return $this->propertyAccessor->getValue($this->metadata, "[$name]");
    }

    public function __set($name, $value)
    {
        $this->createPropertyAccessor();

        if (property_exists($this, $name) === true) {
            $this->propertyAccessor->setValue($this, $name, $value);
        }
        $this->propertyAccessor->setValue($this->metadata, "[$name]", $value);
    }

    public function __isset($name): bool
    {
        return (property_exists($this, $name) || isset($this->metadata[$name]));
    }
}
