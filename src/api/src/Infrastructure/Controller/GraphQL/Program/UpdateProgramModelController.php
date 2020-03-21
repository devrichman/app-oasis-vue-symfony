<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Program;

use App\Application\Program\UpdateProgramModel;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\ProgramModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class UpdateProgramModelController extends AbstractController
{
    private UpdateProgramModel $updateProgramModel;

    public function __construct(UpdateProgramModel $updateProgramModel)
    {
        $this->updateProgramModel = $updateProgramModel;
    }

    /**
     * @throws InvalidStringValue
     * @throws NotFound
     *
     * @Mutation
     * @Logged
     */
    public function updateProgramModel(string $id, string $name, string $description): ProgramModel
    {
        return $this->updateProgramModel->update($id, $name, $description);
    }
}
