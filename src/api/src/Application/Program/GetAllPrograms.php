<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\ProgramRepository;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllPrograms
{
    private ProgramRepository $programRepository;
    private CompanyRepository $companyRepository;

    public function __construct(ProgramRepository $programRepository, CompanyRepository $companyRepository)
    {
        $this->programRepository = $programRepository;
        $this->companyRepository = $companyRepository;
    }

    public function getAll(?string $search, ?string $status, string $sortColumn = 'createdAt', string $sortDirection = 'desc'): ResultIterator
    {
        return $this->programRepository->findByFilters($search, $status, $sortColumn, $sortDirection);
    }
}
