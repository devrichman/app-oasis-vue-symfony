<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use TheCodingMachine\TDBM\ResultIterator;

interface ProgramRepository
{
    public function save(Program $program): void;

    /**
     * @throws NotFound
     */
    public function mustFindOneById(string $id): Program;

    public function findByFilters(?string $search, ?string $status, string $sortColumn = 'createdAt', string $sortDirection = 'desc'): ResultIterator;
}
