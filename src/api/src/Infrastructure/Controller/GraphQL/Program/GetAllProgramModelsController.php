<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Program;

use App\Application\Program\GetAllProgramModels;
use App\Domain\Model\ProgramModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllProgramModelsController extends AbstractController
{
    private GetAllProgramModels $getAllProgramModels;

    public function __construct(GetAllProgramModels $getAllProgramModels)
    {
        $this->getAllProgramModels = $getAllProgramModels;
    }

    /**
     * @return ResultIterator|ProgramModel[]
     *
     * @Query
     * @Logged
     */
    public function getAllProgramModels(?string $search = null, ?string $sortColumn = 'createdAt', ?string $sortDirection = 'desc'): ResultIterator
    {
        /** @var ResultIterator|ProgramModel[] $result */
        $result = $this->getAllProgramModels->getAll($search, $sortColumn, $sortDirection);

        return $result;
    }
}
