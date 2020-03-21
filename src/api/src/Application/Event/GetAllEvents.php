<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Domain\Repository\EventRepository;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllEvents
{
    private EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function getAll(?string $search, ?string $status, string $sortColumn = 'updatedAt', string $sortDirection = 'desc'): ResultIterator
    {
        return $this->eventRepository->findByFilters($search, $status, $sortColumn, $sortDirection);
    }
}
