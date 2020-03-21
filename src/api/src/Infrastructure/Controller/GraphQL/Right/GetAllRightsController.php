<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Right;

use App\Application\Right\GetAllRights;
use App\Domain\Model\Right;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class GetAllRightsController extends AbstractController
{
    private GetAllRights $getAllRights;

    public function __construct(GetAllRights $createRole)
    {
        $this->getAllRights = $createRole;
    }

    /**
     * @return Right[]
     *
     * @Query
     * @Logged
     */
    public function getAllRights(): array
    {
        return $this->getAllRights->getAll();
    }
}
