<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Application\User\DisableUser;
use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class DisableUserController extends AbstractController
{
    private DisableUser $disableUser;

    public function __construct(DisableUser $updateUser)
    {
        $this->disableUser = $updateUser;
    }

    /**
     * @throws NotFound
     *
     * @Mutation()
     * @Logged()
     * @Right("ROLE_DISABLE_USER")
     */
    public function disableUser(
        string $id
    ): User {
        return $this->disableUser->disableUser($id);
    }
}
