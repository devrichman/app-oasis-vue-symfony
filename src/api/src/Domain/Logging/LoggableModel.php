<?php

declare(strict_types=1);

namespace App\Domain\Logging;

use App\Domain\Model\User;
use DateTimeImmutable;

interface LoggableModel
{
    public function setCreatedAt(?DateTimeImmutable $createdAt): void;

    public function getCreatedAt(): ?DateTimeImmutable;

    public function setCreatedBy(?User $user): void;

    public function getCreatedBy(): ?User;

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): void;

    public function getUpdatedAt(): ?DateTimeImmutable;

    public function setUpdatedBy(?User $user): void;

    public function getUpdatedBy(): ?User;
}
