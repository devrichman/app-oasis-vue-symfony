<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Company;
use TheCodingMachine\TDBM\ResultIterator;

interface CompanyRepository
{
    public function save(Company $company): void;

    public function findOneByName(string $name): ?Company;

    public function findOneByCode(string $code): ?Company;

    /**
     * @throws NotFound
     */
    public function mustFindOneById(string $id): Company;

    /**
     * @return Company[]|ResultIterator
     */
    public function findByFilters(?string $search, ?string $sortColumn = null, ?string $sortDirection = 'asc'): ResultIterator;
}
