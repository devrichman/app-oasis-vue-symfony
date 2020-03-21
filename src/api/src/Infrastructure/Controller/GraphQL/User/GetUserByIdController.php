<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Application\User\GetUserById;
use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class GetUserByIdController extends AbstractController
{
    private GetUserById $getUserById;

    public function __construct(GetUserById $getUserById)
    {
        $this->getUserById = $getUserById;
    }

    /**
     * @throws NotFound
     *
     * @Query
     * @Logged
     */
    public function getUserById(string $id): User
    {
        return $this->getUserById->get($id);
    }
}
