<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Role;
use TheCodingMachine\TDBM\ResultIterator;

interface RoleRepository
{
    public function save(Role $role): void;

    public function delete(Role $role): void;

    public function findOneByName(string $name): ?Role;

    /**
     * @throws NotFound
     */
    public function mustFindOneById(string $id): Role;

    /**
     * @throws NotFound
     */
    public function mustFindOneWithNoUsers(string $id): Role;

    /**
     * @return Role[]|ResultIterator
     */
    public function findByFilters(?string $search, ?bool $displayable = true, ?string $sortColumn = null, ?string $sortDirection = null): ResultIterator;

    public function checkNameUnique(string $name, ?string $roleId = null): bool;
}
