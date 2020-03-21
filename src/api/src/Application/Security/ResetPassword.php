<?php

declare(strict_types=1);

namespace App\Application\Security;

use App\Domain\Exception\InvalidStringValue;
use App\Domain\Model\ResetPasswordToken;
use App\Domain\Repository\ResetPasswordTokenRepository;
use App\Domain\Repository\UserRepository;
use Ramsey\Uuid\Uuid;

final class ResetPassword
{
    private UserRepository $userRepository;
    private ResetPasswordTokenRepository $resetPasswordTokenRepository;
    private ResetPasswordNotifier $resetPasswordNotifier;

    public function __construct(UserRepository $userRepository, ResetPasswordTokenRepository $resetPasswordTokenRepository, ResetPasswordNotifier $resetPasswordNotifier)
    {
        $this->userRepository = $userRepository;
        $this->resetPasswordTokenRepository = $resetPasswordTokenRepository;
        $this->resetPasswordNotifier = $resetPasswordNotifier;
    }

    /**
     * @throws InvalidStringValue
     */
    public function reset(string $email): string
    {
        $user = $this->userRepository->findOneByEmail($email);
        if ($user === null) {
            return '';
        }

        $accessToken = Uuid::uuid1()->toString();
        $tokenPassword = $this->resetPasswordTokenRepository->encodeToken($user, $accessToken);

        $resetPasswordToken = $this->resetPasswordTokenRepository->findOneByUser($user);
        if ($resetPasswordToken === null) {
            $resetPasswordToken = new ResetPasswordToken($user, $accessToken);
        } else {
            $resetPasswordToken->setAccessToken($accessToken);
        }
        $this->resetPasswordTokenRepository->save($resetPasswordToken);

        $this->resetPasswordNotifier->notify($user, $tokenPassword);

        return $user->getEmail();
    }
}
