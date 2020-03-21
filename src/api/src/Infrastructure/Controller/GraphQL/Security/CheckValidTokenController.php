<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Security;

use App\Application\Security\CheckValidToken;
use App\Domain\Exception\InvalidValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\ResetPasswordToken;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class CheckValidTokenController extends AbstractController
{
    private CheckValidToken $checkValidToken;

    public function __construct(CheckValidToken $checkValidToken)
    {
        $this->checkValidToken = $checkValidToken;
    }

    /**
     * @throws NotFound
     * @throws InvalidValue
     *
     * @Query
     */
    public function checkValidToken(string $token): ResetPasswordToken
    {
        return $this->checkValidToken->checkValidToken($token);
    }
}
