<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Security;

use App\Application\Security\UpdateMyPassword;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class UpdateMyPasswordController extends AbstractController
{
    private UpdateMyPassword $updateMyPassword;

    public function __construct(UpdateMyPassword $updateMyPassword)
    {
        $this->updateMyPassword = $updateMyPassword;
    }

    /**
     * @throws InvalidStringValue
     *
     * @Mutation
     * @Logged
     */
    public function updateMyPassword(string $password): User
    {
        return $this->updateMyPassword->updateMyPassword($password);
    }
}
