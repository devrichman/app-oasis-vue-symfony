<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\EventModel;
use App\Domain\Repository\EventModelRepository;

final class UpdateEventModel
{
    private EventModelRepository $eventModelRepository;

    public function __construct(EventModelRepository $eventModelRepository)
    {
        $this->eventModelRepository = $eventModelRepository;
    }

    /**
     * @throws NotFound
     * @throws InvalidStringValue
     */
    public function update(string $id, string $name, string $description, string $type): EventModel
    {
        $eventModel = $this->eventModelRepository->mustFindOneById($id);
        $eventModel->setName($name);
        $eventModel->setDescription($description);
        $eventModel->setType($type);

        $this->eventModelRepository->save($eventModel);

        return $eventModel;
    }
}
