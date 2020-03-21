<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Domain\Exception\NotFound;
use App\Domain\Model\EventModel;
use App\Domain\Repository\EventModelRepository;

final class GetEventModelById
{
    private EventModelRepository $eventModelRepository;

    public function __construct(EventModelRepository $eventModelRepository)
    {
        $this->eventModelRepository = $eventModelRepository;
    }

    /**
     * @throws NotFound
     */
    public function get(string $eventModelId): EventModel
    {
        return $this->eventModelRepository->mustFindOneById($eventModelId);
    }
}
