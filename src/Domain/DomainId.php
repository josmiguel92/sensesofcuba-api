<?php


namespace App\Domain;


interface DomainId
{

    /**
     * @internal
     */
    public function __toString(): string;

    /**
     * @param mixed $value
     *
     * @return static
     */
    public static function fromValue($value): self;

    public function isEmpty(): bool;

    public function equals(self $id): bool;

    public function toString(): string;
}
