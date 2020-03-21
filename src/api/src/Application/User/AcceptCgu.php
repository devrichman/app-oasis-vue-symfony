<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Model\User;
use App\Domain\Repository\UserRepository;

final class AcceptCgu
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function acceptCgu(): User
    {
        $user = $this->userRepository->getLoggedUser();

        $user->setCguAccepted(true);

        $this->userRepository->saveNoLog($user);

        return $user;
    }
}
