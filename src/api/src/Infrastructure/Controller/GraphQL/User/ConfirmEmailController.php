<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Application\User\ConfirmEmailUpdate;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\InvalidValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class ConfirmEmailController extends AbstractController
{
    private ConfirmEmailUpdate $confirmEmailUpdate;

    public function __construct(ConfirmEmailUpdate $confirmEmailUpdate)
    {
        $this->confirmEmailUpdate = $confirmEmailUpdate;
    }

    /**
     * @throws InvalidStringValue
     * @throws InvalidValue
     * @throws NotFound
     *
     * @Mutation
     */
    public function confirmEmail(string $token): User
    {
        return $this->confirmEmailUpdate->confirmEmail($token);
    }
}
