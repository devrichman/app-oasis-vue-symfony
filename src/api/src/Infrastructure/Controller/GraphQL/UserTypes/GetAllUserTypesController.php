<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\UserTypes;

use App\Application\UserType\GetAllUserTypes;
use App\Domain\Model\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class GetAllUserTypesController extends AbstractController
{
    private GetAllUserTypes $getAllUserTypes;

    public function __construct(GetAllUserTypes $getAllUserTypes)
    {
        $this->getAllUserTypes = $getAllUserTypes;
    }

    /**
     * @return UserType[]
     *
     * @Query
     * @Logged
     * @Right("ROLE_CREATE_USER")
     */
    public function getAllUserTypes(): array
    {
        return $this->getAllUserTypes->getAll();
    }
}
