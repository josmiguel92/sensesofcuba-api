<?php


namespace App\ORM\Interfaces;

interface MetadataFieldInterface
{
    public function getMetadata(): ?array;

    public function setMetadata(array $metadata): self;

    /**
     * @return array
     * example: return ['useDefaultLanguage' => CheckboxType::class];
     */
    public static function getMetadataFieldTypeList(): array;

    public function __get($name);

    public function __set($name, $value);
}
