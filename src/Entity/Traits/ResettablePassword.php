<?php

namespace App\Entity\Traits;

use App\Message\FinishPasswordRequest;
use App\Message\PasswordRequest;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;

trait ResettablePassword
{



    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    public function getPasswordRequestedAt(): ?DateTimeInterface
    {
        return $this->passwordRequestedAt;
    }

    public function requestPassword(?string $token = null): void
    {
        try {
            $this->passwordResetToken = $token ?? bin2hex(random_bytes(32));
        } catch (Exception $e) {
            $this->passwordResetToken = md5(mt_rand());
        }
        $this->passwordRequestedAt = new DateTimeImmutable();
    }

    public function abortPasswordRequest(): void
    {
        $this->passwordResetToken = null;
        $this->passwordRequestedAt = null;
    }

    private function onRequestPasswordEvent(PasswordRequest $event): bool
    {
        $this->requestPassword($event->token);

        return true;
    }


    private function onFinishPasswordRequestEvent(FinishPasswordRequest $event): bool
    {
        if (null === $this->passwordRequestedAt) {
            return false;
        }

        $this->abortPasswordRequest();

        return true;
    }
}
