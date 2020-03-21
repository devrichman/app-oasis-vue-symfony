<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Domain\Repository\ProgramModelRepository;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllProgramModels
{
    private ProgramModelRepository $programModelRepository;

    public function __construct(ProgramModelRepository $programModelRepository)
    {
        $this->programModelRepository = $programModelRepository;
    }

    public function getAll(?string $search = null, ?string $sortColumn = 'createdAt', ?string $sortDirection = 'desc'): ResultIterator
    {
        return $this->programModelRepository->findByFilters($search, $sortColumn, $sortDirection);
    }
}
