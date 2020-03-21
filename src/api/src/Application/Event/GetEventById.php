<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Repository\EventRepository;

final class GetEventById
{
    private EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @throws NotFound
     */
    public function get(string $eventId): Event
    {
        return $this->eventRepository->mustFindOneById($eventId);
    }
}
