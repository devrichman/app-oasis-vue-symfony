<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Repository\EventModelRepository;
use App\Domain\Repository\EventRepository;
use App\Domain\Repository\UserRepository;
use Safe\DateTimeImmutable;
use Safe\Exceptions\DatetimeException;

final class UpdateEvent
{
    private EventRepository $eventRepository;
    private EventModelRepository $eventModelRepository;
    private UserRepository $userRepository;

    public function __construct(EventRepository $eventRepository, EventModelRepository $eventModelRepository, UserRepository $userRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->eventModelRepository = $eventModelRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param string[] $userIds
     *
     * @throws NotFound
     * @throws InvalidDateValue
     * @throws InvalidStringValue
     */
    public function update(string $id, string $name, string $description, string $type, array $userIds, ?string $organizerId = null, ?string $dateEvent = null, ?string $modelId = null): Event
    {
        $event = $this->eventRepository->mustFindOneById($id);

        if (! empty($modelId)) {
            $eventModel = $this->eventModelRepository->mustFindOneById($modelId);
            $event->setEventModel($eventModel);
        }

        if (! empty($organizerId)) {
            $organizer = $this->userRepository->mustFindOneById($organizerId);
            $event->setOrganizer($organizer);
        }
        $userBeans = [];
        foreach ($userIds as $user) {
            $userBeans[] = $this->userRepository->mustFindOneById($user);
        }
        $event->setUsers($userBeans);

        $event->setName($name);
        $event->setDescription($description);
        $event->setType($type);

        if (! empty($dateEvent)) {
            try {
                $event->setDateEvent(new DateTimeImmutable($dateEvent));
            } catch (DatetimeException $e) {
                throw new InvalidDateValue('Input dateEvent is in wrong format', 400, $e);
            }
        }
        $this->eventRepository->save($event);

        return $event;
    }
}
