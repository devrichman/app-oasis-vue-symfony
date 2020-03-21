<?php

declare(strict_types=1);

namespace App\Application\Role;

use App\Domain\Exception\Exist;
use App\Domain\Model\User;
use App\Domain\Repository\RoleRepository;

final class RoleNameUnique
{
    private RoleRepository $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @throws Exist
     */
    public function roleNameUnique(
        string $name,
        ?string $roleId = null
    ): bool {
        if (! $this->roleRepository->checkNameUnique($name, $roleId)) {
            throw new Exist(User::class, [], true);
        }

        return true;
    }
}
