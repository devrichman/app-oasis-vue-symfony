<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Program;

use App\Application\Program\DeleteProgramModel;
use App\Domain\Exception\NotFound;
use App\Domain\Exception\UnableDelete;
use App\Domain\Model\ProgramModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class DeleteProgramModelController extends AbstractController
{
    private DeleteProgramModel $deleteProgramModel;

    public function __construct(DeleteProgramModel $deleteProgramModel)
    {
        $this->deleteProgramModel = $deleteProgramModel;
    }

    /**
     * @throws NotFound
     * @throws UnableDelete
     *
     * @Mutation
     * @Logged
     */
    public function deleteProgramModel(string $programModelId): ProgramModel
    {
        return $this->deleteProgramModel->delete($programModelId);
    }
}
