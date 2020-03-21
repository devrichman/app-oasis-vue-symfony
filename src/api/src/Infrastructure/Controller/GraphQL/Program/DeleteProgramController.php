<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Program;

use App\Application\Program\DeleteProgram;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class DeleteProgramController extends AbstractController
{
    private DeleteProgram $deleteProgram;

    public function __construct(DeleteProgram $deleteProgram)
    {
        $this->deleteProgram = $deleteProgram;
    }

    /**
     * @throws NotFound
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_DELETE_PROGRAM")
     */
    public function deleteProgram(string $programId): Program
    {
        return $this->deleteProgram->delete($programId);
    }
}
