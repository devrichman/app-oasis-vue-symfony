<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use App\Domain\Repository\RoleRepository;
use App\Domain\Repository\UserRepository;

final class AddRoleToUser
{
    private UserRepository $userRepository;
    private RoleRepository $roleRepository;

    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * @throws NotFound
     */
    public function add(string $userId, string $roleId): User
    {
        $user = $this->userRepository->mustFindOneById($userId);
        $role = $this->roleRepository->mustFindOneById($roleId);

        $user->addRoleByUsersRoles($role);
        $this->userRepository->save($user);

        return $user;
    }
}
