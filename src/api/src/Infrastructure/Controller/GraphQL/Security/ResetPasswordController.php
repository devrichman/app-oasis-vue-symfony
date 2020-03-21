<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Security;

use App\Application\Security\ResetPassword;
use App\Domain\Exception\InvalidStringValue;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class ResetPasswordController extends AbstractController
{
    private ResetPassword $resetPassword;

    public function __construct(ResetPassword $resetPassword)
    {
        $this->resetPassword = $resetPassword;
    }

    /**
     * @throws InvalidStringValue
     *
     * @Mutation
     */
    public function resetPassword(string $email): string
    {
        return $this->resetPassword->reset($email);
    }
}
