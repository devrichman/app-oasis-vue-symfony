<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\InvalidValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\UpdateEmailToken;
use App\Domain\Model\User;

interface EmailTokenRepository
{
    public function save(UpdateEmailToken $emailToken): void;

    public function delete(UpdateEmailToken $emailToken, bool $cascade = false): void;

    public function encodeToken(User $user, string $accessToken): string;

    /**
     * @throws InvalidValue
     * @throws NotFound
     */
    public function mustCheckValidToken(string $token): UpdateEmailToken;

    /**
     * @throws NotFound
     */
    public function findOneByUserId(string $id): ?UpdateEmailToken;
}
