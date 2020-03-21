<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use App\Domain\Repository\UserRepository;

final class EnableUser
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @throws NotFound
     */
    public function enableUser(
        string $id
    ): User {
        $user = $this->userRepository->mustFindOneById($id);

        $user->setStatus(true);

        $this->userRepository->save($user);

        return $user;
    }
}
