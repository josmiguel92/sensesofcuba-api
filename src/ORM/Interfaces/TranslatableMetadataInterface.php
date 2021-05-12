<?php


namespace App\ORM\Interfaces;


interface TranslatableMetadataInterface
{

    public function getMetadata(): ?array;

    public function setMetadata(array $metadata): self;

    public function getLangList(): array;

    public function getTranslatableMetadataNames(): array;

}
