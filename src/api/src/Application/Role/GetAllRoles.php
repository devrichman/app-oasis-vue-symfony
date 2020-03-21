<?php

declare(strict_types=1);

namespace App\Application\Role;

use App\Domain\Repository\RoleRepository;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllRoles
{
    private RoleRepository $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function getAll(?string $search = null, ?bool $displayable = true, ?string $sortColumn = null, ?string $sortDirection = null): ResultIterator
    {
        return $this->roleRepository->findByFilters($search, $displayable, $sortColumn, $sortDirection);
    }
}
