<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use App\Domain\Repository\UserRepository;

final class DeleteUser
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @throws NotFound
     * @throws InvalidStringValue
     */
    public function deleteUser(
        string $id
    ): User {
        $user = $this->userRepository->mustFindOneById($id);

        $email = $user->getEmail();
        $user->setEmail($email . '-archived');
        $user->setDeleted(true);

        $this->userRepository->save($user);

        return $user;
    }
}
