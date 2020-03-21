<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Role;

use App\Application\Role\RoleNameUnique;
use App\Domain\Exception\Exist;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;

class RoleNameUniqueController
{
    private RoleNameUnique $roleNameUnique;

    public function __construct(RoleNameUnique $roleNameUnique)
    {
        $this->roleNameUnique = $roleNameUnique;
    }

    /**
     * @throws Exist
     *
     * @Query()
     * @Logged()
     */
    public function roleNameUnique(
        string $name,
        ?string $roleId = null
    ): bool {
        return $this->roleNameUnique->roleNameUnique($name, $roleId);
    }
}
