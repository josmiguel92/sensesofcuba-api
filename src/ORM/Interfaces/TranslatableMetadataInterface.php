<?php


namespace App\ORM\Interfaces;


interface TranslatableMetadataInterface
{

    public function getMetadata(): ?array;

    public function setMetadata(array $metadata): self;

    public static function getLangList(): array;

    public static function getTranslatableMetadataNames(): array;

}
