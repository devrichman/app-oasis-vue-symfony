<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Program;

use App\Application\Program\UpdateProgram;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class UpdateProgramController extends AbstractController
{
    private UpdateProgram $updateProgram;
    private TokenStorageInterface $tokenStorage;

    public function __construct(UpdateProgram $updateProgram, TokenStorageInterface $tokenStorage)
    {
        $this->updateProgram = $updateProgram;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param string[] $userIds
     *
     * @throws NotFound
     * @throws InvalidStringValue
     * @throws InvalidDateValue
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_CREATE_PROGRAM")
     */
    public function updateProgram(string $id, string $name, string $description, string $type, array $userIds, ?string $coachId = null, ?string $modelId = null, ?string $dateStart = null, ?string $dateEnd = null): Program
    {
        return $this->updateProgram->update($id, $name, $description, $type, $userIds, $coachId, $modelId, $dateStart, $dateEnd);
    }
}
