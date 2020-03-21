<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Role;

use App\Application\Role\DeleteRole;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class DeleteRoleController extends AbstractController
{
    private DeleteRole $deleteRole;

    public function __construct(DeleteRole $deleteRole)
    {
        $this->deleteRole = $deleteRole;
    }

    /**
     * @throws NotFound
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_DELETE_ROLE")
     */
    public function deleteRole(string $roleId): Role
    {
        return $this->deleteRole->delete($roleId);
    }
}
