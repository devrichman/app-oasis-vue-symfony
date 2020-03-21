<?php

declare(strict_types=1);

namespace App\Tests\Application\Program;

use App\Application\Program\AddEventToProgram;
use App\Domain\Enum\EventTypeEnum;
use App\Domain\Enum\ProgramTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Model\Program;
use App\Domain\Repository\EventRepository;
use App\Domain\Repository\ProgramRepository;
use App\Tests\Application\ApplicationTestCase;
use DateTime;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use function array_map;
use function preg_quote;

class AddEventToProgramTest extends ApplicationTestCase
{
    protected const NUM_EVENTS = 5;

    protected AddEventToProgram $addEventToProgram;
    protected Program $program;

    /** @var Event[] $events */
    protected array $events;

    protected function setUp(): void
    {
        parent::setUp();
        $this->addEventToProgram = self::$container->get(AddEventToProgram::class);
        $programRepository = self::$container->get(ProgramRepository::class);
        $eventRepository = self::$container->get(EventRepository::class);

        $this->program = new Program($this->faker->name, $this->faker->text, ProgramTypeEnum::INDIVIDUAL);
        $programRepository->save($this->program);

        $modelEvent = new Event($this->faker->name, $this->faker->text, EventTypeEnum::INDIVIDUAL_SESSION);
        $eventRepository->save($modelEvent);

        $eventDate = null;
        for ($i = 0; $i < self::NUM_EVENTS; $i++) {
            $eventDate = $this->faker->dateTimeBetween(! ($eventDate instanceof DateTime) ? 'now' : $eventDate, '+10 days');
            $event = new Event($this->faker->name, $this->faker->text, EventTypeEnum::INDIVIDUAL_SESSION);
            $event->setOrganizer($this->loggedUser);
            $event->setDateEvent(DateTimeImmutable::createFromFormat(DateTimeImmutable::ISO8601, $eventDate->format('c')));
            $eventRepository->save($event);

            $this->events[] = $event;
        }
    }

    public function testAddEventToProgram(): void
    {
        foreach ($this->events as $event) {
            $this->addEventToProgram->add($this->program->getId(), $event->getId());
            $this->assertContains($event->getId(), array_map(static fn(Event $event) => $event->getId(), $this->program->getEvents()->toArray()));
            $this->assertEquals($this->program->getId(), $event->getProgram()->getId());
        }
        $this->assertEquals($this->events[0]->getDateEvent()->getTimestamp(), $this->program->getDateStart()->getTimestamp());
        $this->assertEquals($this->events[self::NUM_EVENTS - 1]->getDateEvent()->getTimestamp(), $this->program->getDateEnd()->getTimestamp());
    }

    public function testInvalidProgram(): void
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionMessageMatches('/' . preg_quote(Program::class) . '/');
        $this->addEventToProgram->add(Uuid::uuid1()->toString(), $this->events[0]->getId());
    }

    public function testInvalidEvent(): void
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionMessageMatches('/' . preg_quote(Event::class) . '/');
        $this->addEventToProgram->add($this->program->getId(), Uuid::uuid1()->toString());
    }
}
