<?php

declare(strict_types=1);

namespace App\Application\Security;

use App\Domain\Exception\InvalidValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\ResetPasswordToken;
use App\Domain\Repository\ResetPasswordTokenRepository;

class CheckValidToken
{
    private ResetPasswordTokenRepository $resetPasswordTokenRepository;

    public function __construct(ResetPasswordTokenRepository $resetPasswordTokenRepository)
    {
        $this->resetPasswordTokenRepository = $resetPasswordTokenRepository;
    }

    /**
     * @throws NotFound
     * @throws InvalidValue
     */
    public function checkValidToken(string $token): ResetPasswordToken
    {
        return $this->resetPasswordTokenRepository->mustCheckValidToken($token);
    }
}
