<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\NotFound;
use App\Domain\Model\ProgramModel;
use TheCodingMachine\TDBM\ResultIterator;

interface ProgramModelRepository
{
    public function save(ProgramModel $programModel): void;

    /**
     * @throws NotFound
     */
    public function mustFindOneById(string $id): ProgramModel;

    public function findByFilters(?string $search, ?string $sortColumn = 'createdAt', ?string $sortDirection = 'desc'): ResultIterator;
}
