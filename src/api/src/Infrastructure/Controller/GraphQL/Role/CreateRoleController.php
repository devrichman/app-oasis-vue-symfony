<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Role;

use App\Application\Role\CreateRole;
use App\Domain\Exception\Exist;
use App\Domain\Exception\InvalidArrayValue;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class CreateRoleController extends AbstractController
{
    private CreateRole $createRole;

    public function __construct(CreateRole $createRole)
    {
        $this->createRole = $createRole;
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
     * @Right("ROLE_CREATE_ROLE"))
     */
    public function createRole(string $name, string $description, array $rights): Role
    {
        return $this->createRole->create($name, $description, $rights);
    }
}
