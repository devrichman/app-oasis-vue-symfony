<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Domain\Exception\NotFound;
use App\Domain\Model\EventModel;
use App\Domain\Repository\EventModelRepository;
use App\Domain\Repository\ProgramModelRepository;

final class CreateEventModel
{
    private EventModelRepository $eventModelRepository;
    private ProgramModelRepository $programModelRepository;

    public function __construct(EventModelRepository $eventModelRepository, ProgramModelRepository $programModelRepository)
    {
        $this->eventModelRepository = $eventModelRepository;
        $this->programModelRepository = $programModelRepository;
    }

    /**
     * @throws NotFound
     */
    public function create(string $name, string $description, string $type, ?string $programModelId = null): EventModel
    {
        $eventModel = new EventModel($name, $description, $type);

        if ($programModelId !== null) {
            $programModel = $this->programModelRepository->mustFindOneById($programModelId);
            $eventModel->setProgramModel($programModel);
        }

        $this->eventModelRepository->save($eventModel);

        return $eventModel;
    }
}
