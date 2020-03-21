<?php

declare(strict_types=1);

namespace App\Application\Role;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Role;
use App\Domain\Repository\RoleRepository;

final class GetRoleById
{
    private RoleRepository $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @throws NotFound
     */
    public function get(string $id): Role
    {
        return $this->roleRepository->mustFindOneById($id);
    }
}
