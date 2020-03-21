<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Application\User\DeleteUser;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

class DeleteUserController
{
    private DeleteUser $deleteUser;

    public function __construct(DeleteUser $deleteUser)
    {
        $this->deleteUser = $deleteUser;
    }

    /**
     * @throws NotFound
     * @throws InvalidStringValue
     *
     * @Mutation()
     * @Logged()
     * @Right("ROLE_DELETE_USER")
     */
    public function deleteUser(
        string $id
    ): User {
        return $this->deleteUser->deleteUser($id);
    }
}
