<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Role;

use App\Application\Role\GetAllRoles;
use App\Domain\Model\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllRolesController extends AbstractController
{
    private GetAllRoles $getAllRoles;

    public function __construct(GetAllRoles $getAllRoles)
    {
        $this->getAllRoles = $getAllRoles;
    }

    /**
     * @return ResultIterator|Role[]
     *
     * @Query
     * @Logged
     */
    public function getAllRoles(?string $search = null, ?bool $displayable = true, ?string $sortColumn = null, ?string $sortDirection = null): ResultIterator
    {
        /** @var ResultIterator|Role[] $result */
        $result = $this->getAllRoles->getAll($search, $displayable, $sortColumn, $sortDirection);

        return $result;
    }
}
