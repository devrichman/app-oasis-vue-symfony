<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\InvalidValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\ResetPasswordToken;
use App\Domain\Model\User;

interface ResetPasswordTokenRepository
{
    public function encodeToken(User $user, string $accessToken): string;

    public function findOneByUser(User $user): ?ResetPasswordToken;

    /**
     * @throws InvalidValue
     * @throws NotFound
     */
    public function mustCheckValidToken(string $token): ResetPasswordToken;

    public function save(ResetPasswordToken $token): void;

    public function delete(ResetPasswordToken $toke): void;
}
