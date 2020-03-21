<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\NotFound;
use App\Domain\Model\EventModel;
use TheCodingMachine\TDBM\ResultIterator;

interface EventModelRepository
{
    public function save(EventModel $programModel): void;

    /**
     * @throws NotFound
     */
    public function mustFindOneById(string $id): EventModel;

    public function findByFilters(?string $search = null, ?string $sortColumn = 'updatedAt', ?string $sortDirection = 'desc'): ResultIterator;
}
