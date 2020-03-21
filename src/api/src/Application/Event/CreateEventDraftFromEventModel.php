<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Application\Program\AddEventToProgram;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Repository\EventModelRepository;
use App\Domain\Repository\EventRepository;
use App\Domain\Repository\UserRepository;

final class CreateEventDraftFromEventModel
{
    private EventRepository $eventRepository;
    private EventModelRepository $eventModelRepository;
    private UserRepository $userRepository;
    private AddEventToProgram $addEventToProgram;

    public function __construct(EventRepository $eventRepository, EventModelRepository $eventModelRepository, UserRepository $userRepository, AddEventToProgram $addEventToProgram)
    {
        $this->eventRepository = $eventRepository;
        $this->eventModelRepository = $eventModelRepository;
        $this->userRepository = $userRepository;
        $this->addEventToProgram = $addEventToProgram;
    }

    /**
     * @param string[] $userIds
     *
     * @throws NotFound
     * @throws InvalidDateValue
     */
    public function create(string $modelId, array $userIds, ?string $organizerId = null, ?string $programId = null): Event
    {
        $eventModel = $this->eventModelRepository->mustFindOneById($modelId);

        $event = new Event($eventModel->getName(), $eventModel->getDescription(), $eventModel->getType());
        $event->setEventModel($eventModel);

        if (! empty($organizerId)) {
            $organizer = $this->userRepository->mustFindOneById($organizerId);
            $event->setOrganizer($organizer);
        }

        $userBeans = [];
        foreach ($userIds as $user) {
            $userBeans[] = $this->userRepository->mustFindOneById($user);
        }
        $event->setUsers($userBeans);

        $this->eventRepository->save($event);

        if ($programId) {
            $this->addEventToProgram->add($programId, $event->getId());
        }

        return $event;
    }
}
