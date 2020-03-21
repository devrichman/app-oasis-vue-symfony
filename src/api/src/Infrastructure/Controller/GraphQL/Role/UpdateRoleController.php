<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Role;

use App\Application\Role\UpdateRole;
use App\Domain\Exception\Exist;
use App\Domain\Exception\InvalidArrayValue;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class UpdateRoleController extends AbstractController
{
    private UpdateRole $updateRole;

    public function __construct(UpdateRole $updateRole)
    {
        $this->updateRole = $updateRole;
    }

    /**
     * @param string[] $rights
     *
     * @throws Exist
     * @throws NotFound
     * @throws InvalidStringValue
     * @throws InvalidArrayValue
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_UPDATE_ROLE"))
     */
    public function updateRole(string $id, string $name, string $description, array $rights): Role
    {
        return $this->updateRole->update($id, $name, $description, $rights);
    }
}
