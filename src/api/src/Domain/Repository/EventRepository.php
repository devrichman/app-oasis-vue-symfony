<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use TheCodingMachine\TDBM\ResultIterator;

interface EventRepository
{
    public function save(Event $event): void;

    public function delete(Event $event): void;

    /**
     * @throws NotFound
     */
    public function mustFindOneById(string $id): Event;

    public function findByFilters(?string $search, ?string $status, string $sortColumn = 'updatedAt', string $sortDirection = 'desc'): ResultIterator;
}
