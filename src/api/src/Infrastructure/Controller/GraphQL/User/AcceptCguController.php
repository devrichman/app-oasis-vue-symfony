<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Application\User\AcceptCgu;
use App\Domain\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class AcceptCguController extends AbstractController
{
    private AcceptCgu $acceptCgu;

    public function __construct(AcceptCgu $acceptCgu)
    {
        $this->acceptCgu = $acceptCgu;
    }

    /**
     * @Mutation
     * @Logged
     */
    public function acceptCgu(): User
    {
        return $this->acceptCgu->acceptCgu();
    }
}
