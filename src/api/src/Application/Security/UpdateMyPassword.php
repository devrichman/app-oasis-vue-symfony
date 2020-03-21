<?php

declare(strict_types=1);

namespace App\Application\Security;

use App\Domain\Exception\InvalidStringValue;
use App\Domain\Model\User;
use App\Domain\Repository\UserRepository;

final class UpdateMyPassword
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @throws InvalidStringValue
     */
    public function updateMyPassword(string $password): User
    {
        $user = $this->userRepository->getLoggedUser();

        $user->setPassword($password);

        $this->userRepository->saveNoLog($user);

        return $user;
    }
}
