<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Domain\Exception\NotFound;
use App\Domain\Exception\UnableDelete;
use App\Domain\Model\EventModel;
use App\Domain\Repository\EventModelRepository;

final class DeleteEventModel
{
    private EventModelRepository $eventModelRepository;

    public function __construct(EventModelRepository $eventModelRepository)
    {
        $this->eventModelRepository = $eventModelRepository;
    }

    /**
     * @throws NotFound
     * @throws UnableDelete
     */
    public function delete(string $id): EventModel
    {
        $eventModel = $this->eventModelRepository->mustFindOneById($id);
        UnableDelete::mustNotHaveEvents($eventModel);
        $eventModel->setDeleted(true);
        $this->eventModelRepository->save($eventModel);

        return $eventModel;
    }
}
