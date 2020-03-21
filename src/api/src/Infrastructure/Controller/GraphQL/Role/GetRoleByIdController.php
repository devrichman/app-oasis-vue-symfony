<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Role;

use App\Application\Role\GetRoleById;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class GetRoleByIdController extends AbstractController
{
    private GetRoleById $getRole;

    public function __construct(GetRoleById $getRole)
    {
        $this->getRole = $getRole;
    }

    /**
     * @throws NotFound
     *
     * @Query
     * @Logged
     */
    public function getRoleById(string $id): Role
    {
        return $this->getRole->get($id);
    }
}
