<?php

namespace App\Entity\Traits;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

trait CanBeConfirmed
{

    /** @var null|string */
    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private ?string $confirmationToken;

    /** @var null|DateTimeInterface */
    /**
     * @ORM\Column(type="datetime", length=180, nullable=true)
     */
    private ?DateTimeInterface $confirmedAt;

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function getConfirmedAt(): ?DateTimeInterface
    {
        return $this->confirmedAt;
    }

    public function isConfirmed(): bool
    {
        return null !== $this->confirmedAt;
    }

    public function confirm(): void
    {
        $this->confirmationToken = null;
        $this->confirmedAt = new DateTimeImmutable();
    }
}
