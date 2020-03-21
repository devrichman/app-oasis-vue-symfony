<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Program;

use App\Application\Program\CreateProgram;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class CreateProgramController extends AbstractController
{
    private CreateProgram $createProgram;
    private TokenStorageInterface $tokenStorage;

    public function __construct(CreateProgram $createProgram, TokenStorageInterface $tokenStorage)
    {
        $this->createProgram = $createProgram;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param string[] $userIds
     *
     * @throws NotFound
     * @throws InvalidDateValue
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_CREATE_PROGRAM")
     */
    public function createProgram(string $name, string $type, string $description, array $userIds, ?string $coachId = null, ?string $modelId = null): Program
    {
        return $this->createProgram->create($name, $description, $type, $userIds, $coachId, $modelId);
    }
}
