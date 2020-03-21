<?php

declare(strict_types=1);

namespace App\Application\Company;

use App\Domain\Repository\CompanyRepository;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllCompanies
{
    private CompanyRepository $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function getAll(?string $search, ?string $sortColumn = null, ?string $sortDirection = null): ResultIterator
    {
        return $this->companyRepository->findByFilters($search, $sortColumn, $sortDirection);
    }
}
