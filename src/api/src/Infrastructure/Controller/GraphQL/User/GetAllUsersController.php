<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Application\User\GetAllUsers;
use App\Domain\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Security;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllUsersController extends AbstractController
{
    private GetAllUsers $getAllUsers;

    public function __construct(GetAllUsers $getAllUsers)
    {
        $this->getAllUsers = $getAllUsers;
    }

    /**
     * @return ResultIterator|User[]
     *
     * @Query
     * @Logged
     * @Security("user.getType() !== 'candidat'")
     */
    public function getAllUsers(?string $search = null, ?string $companyName = null, bool $coachesOnly = false, ?string $role = null, ?string $companyId = null, ?string $sortColumn = 'createdAt', ?string $sortDirection = 'desc'): ResultIterator
    {
        /** @var ResultIterator|User[] $result */
        $result = $this->getAllUsers->getAll($search, $companyName, $coachesOnly, $role, $companyId, $sortColumn, $sortDirection);

        return $result;
    }
}
