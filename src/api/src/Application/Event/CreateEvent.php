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
use Safe\DateTimeImmutable;
use Safe\Exceptions\DatetimeException;

final class CreateEvent
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
    public function create(string $name, string $description, string $type, array $userIds, ?string $organizerId = null, ?string $dateEvent = null, ?string $modelId = null, ?string $programId = null): Event
    {
        $event = new Event($name, $description, $type);

        if (! empty($modelId)) {
            $eventModel = $this->eventModelRepository->mustFindOneById($modelId);
            $event->setEventModel($eventModel);
        }

        if (! empty($organizerId)) {
            $organizer = $this->userRepository->mustFindOneById($organizerId);
            $event->setOrganizer($organizer);
        }

        if (! empty($dateEvent)) {
            try {
                $event->setDateEvent(new DateTimeImmutable($dateEvent));
            } catch (DatetimeException $e) {
                throw new InvalidDateValue('Input dateEvent is in wrong format', 400, $e);
            }
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
