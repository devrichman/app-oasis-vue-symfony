<?php

declare(strict_types=1);

namespace App\Application\Role;

use App\Domain\Exception\Exist;
use App\Domain\Exception\InvalidArrayValue;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Role;
use App\Domain\Repository\RightRepository;
use App\Domain\Repository\RoleRepository;
use function array_map;

final class CreateRole
{
    private RoleRepository $roleRepository;
    private RightRepository $rightRepository;

    public function __construct(RoleRepository $roleRepository, RightRepository $rightRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->rightRepository = $rightRepository;
    }

    /**
     * @param string[] $rights
     *
     * @throws NotFound
     * @throws InvalidStringValue
     * @throws InvalidArrayValue
     * @throws Exist
     */
    public function create(string $name, string $description, array $rights): Role
    {
        $existingRole = $this->roleRepository->findOneByName($name);
        if ($existingRole !== null) {
            throw new Exist(Role::class, [], true);
        }

        $role = new Role($name);
        $role->setDescription($description);
        $role->setRights(array_map(fn(string $right) => $this->rightRepository->mustFindOneByCode($right), $rights));
        $this->roleRepository->save($role);

        return $role;
    }
}
