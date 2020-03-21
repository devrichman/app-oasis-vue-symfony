<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\NotFound;
use App\Domain\Repository\EventRepository;
use App\Domain\Repository\ProgramRepository;

final class AddEventToProgram
{
    private ProgramRepository $programRepository;
    private EventRepository $eventRepository;

    public function __construct(ProgramRepository $programRepository, EventRepository $eventRepository)
    {
        $this->programRepository = $programRepository;
        $this->eventRepository = $eventRepository;
    }

    /**
     * @throws NotFound
     * @throws InvalidDateValue
     */
    public function add(string $programId, string $eventId): void
    {
        $program = $this->programRepository->mustFindOneById($programId);
        $event = $this->eventRepository->mustFindOneById($eventId);

        $event->setProgram($program);
        $this->eventRepository->save($event);

        $events = $program->getEvents();
        $minDate = $program->getDateStart();
        $maxDate = $program->getDateEnd();
        foreach ($events as $event) {
            if (! $maxDate || $event->getDateEvent() > $maxDate) {
                $maxDate = $event->getDateEvent();
            }
            if ($minDate && $event->getDateEvent() >= $minDate) {
                continue;
            }

            $minDate = $event->getDateEvent();
        }
        $program->setDateStart($minDate);
        $program->setDateEnd($maxDate);
        $this->programRepository->save($program);
    }
}
