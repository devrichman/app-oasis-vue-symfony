<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Application\User\EnableUser;
use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class EnableUserController extends AbstractController
{
    private EnableUser $enableUser;

    public function __construct(EnableUser $updateUser)
    {
        $this->enableUser = $updateUser;
    }

    /**
     * @throws NotFound
     *
     * @Mutation()
     * @Logged()
     */
    public function enableUser(
        string $id
    ): User {
        return $this->enableUser->enableUser($id);
    }
}
