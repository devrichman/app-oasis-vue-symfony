<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Domain\Repository\EventModelRepository;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllEventModels
{
    private EventModelRepository $eventModelRepository;

    public function __construct(EventModelRepository $eventModelRepository)
    {
        $this->eventModelRepository = $eventModelRepository;
    }

    public function getAll(?string $search = null, ?string $sortColumn = 'createdAt', ?string $sortDirection = 'desc'): ResultIterator
    {
        return $this->eventModelRepository->findByFilters($search, $sortColumn, $sortDirection);
    }
}
