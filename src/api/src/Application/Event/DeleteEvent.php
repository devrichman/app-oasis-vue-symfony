<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Repository\EventRepository;

final class DeleteEvent
{
    private EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @throws NotFound
     */
    public function delete(string $id): Event
    {
        $event = $this->eventRepository->mustFindOneById($id);

        $this->eventRepository->delete($event);

        return $event;
    }
}
