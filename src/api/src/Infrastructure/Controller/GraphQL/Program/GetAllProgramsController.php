<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Program;

use App\Application\Program\GetAllPrograms;
use App\Domain\Model\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllProgramsController extends AbstractController
{
    private GetAllPrograms $getAllPrograms;

    public function __construct(GetAllPrograms $getAllPrograms)
    {
        $this->getAllPrograms = $getAllPrograms;
    }

    /**
     * @return ResultIterator|Program[]
     *
     * @Query
     * @Logged
     */
    public function getAllPrograms(?string $search = null, ?string $status = null, string $sortColumn = 'createdAt', string $sortDirection = 'desc'): ResultIterator
    {
        /** @var ResultIterator|Program[] $result */
        $result = $this->getAllPrograms->getAll($search, $status, $sortColumn, $sortDirection);

        return $result;
    }
}
