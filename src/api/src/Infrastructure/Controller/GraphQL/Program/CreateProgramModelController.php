<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Program;

use App\Application\Program\CreateProgramModel;
use App\Domain\Model\ProgramModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class CreateProgramModelController extends AbstractController
{
    private CreateProgramModel $createProgramModel;

    public function __construct(CreateProgramModel $createProgramModel)
    {
        $this->createProgramModel = $createProgramModel;
    }

    /**
     * @Logged
     * @Mutation
     */
    public function createProgramModel(string $name, string $description): ProgramModel
    {
        return $this->createProgramModel->create($name, $description);
    }
}
