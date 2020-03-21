<?php

declare(strict_types=1);

namespace App\Application\Security;

use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\InvalidValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use App\Domain\Repository\ResetPasswordTokenRepository;
use App\Domain\Repository\UserRepository;

final class UpdatePassword
{
    private UserRepository $userRepository;
    private ResetPasswordTokenRepository $resetPasswordTokenRepository;

    public function __construct(UserRepository $userRepository, ResetPasswordTokenRepository $resetPasswordTokenRepository)
    {
        $this->userRepository = $userRepository;
        $this->resetPasswordTokenRepository = $resetPasswordTokenRepository;
    }

    /**
     * @throws NotFound
     * @throws InvalidValue
     * @throws InvalidStringValue
     */
    public function update(string $token, string $password): User
    {
        $resetPasswordToken = $this->resetPasswordTokenRepository->mustCheckValidToken($token);
        $user = $resetPasswordToken->getUser();

        $user->setPassword($password);

        $this->userRepository->saveNoLog($user);
        $this->resetPasswordTokenRepository->delete($resetPasswordToken);

        return $user;
    }
}
