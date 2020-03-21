<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Security;

use App\Application\Security\UpdatePassword;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\InvalidValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class UpdatePasswordController extends AbstractController
{
    private UpdatePassword $updatePassword;

    public function __construct(UpdatePassword $updatePassword)
    {
        $this->updatePassword = $updatePassword;
    }

    /**
     * @throws NotFound
     * @throws InvalidValue
     * @throws InvalidStringValue
     *
     * @Mutation
     */
    public function updatePassword(string $token, string $password): User
    {
        return $this->updatePassword->update($token, $password);
    }
}
