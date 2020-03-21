<?php

declare(strict_types=1);

namespace App\Tests\Application\Event;

use App\Application\Event\CreateEvent;
use App\Domain\Enum\EventTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\EventModel;
use App\Domain\Model\User;
use App\Domain\Repository\EventModelRepository;
use App\Tests\Application\ApplicationTestCase;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use function preg_quote;

class CreateEventTest extends ApplicationTestCase
{
    protected CreateEvent $createEvent;
    protected EventModel $eventModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createEvent = self::$container->get(CreateEvent::class);

        $this->eventModel = new EventModel($this->faker->name, $this->faker->text, EventTypeEnum::INDIVIDUAL_SESSION);
        self::$container->get(EventModelRepository::class)->save($this->eventModel);
    }

    public function testCreateEvent(): void
    {
        $name = $this->faker->name;
        $description = $this->faker->text;
        $type = EventTypeEnum::INDIVIDUAL_SESSION;
        $dateEvent = $this->faker->dateTimeBetween('+1 day', '+30 days');

        // Program parameter is tested separately in AddEventToProgramTest
        $event = $this->createEvent->create(
            $name,
            $description,
            $type,
            [$this->loggedUser->getId()],
            $this->loggedUser->getId(),
            $dateEvent->format(DateTimeImmutable::ISO8601),
            $this->eventModel->getId(),
        );

        $this->assertEquals($name, $event->getName());
        $this->assertEquals($description, $event->getDescription());
        $this->assertEquals($dateEvent->getTimestamp(), $event->getDateEvent()->getTimestamp());
        $this->assertEquals($this->eventModel->getId(), $event->getEventModel()->getId());
        $this->assertEquals($this->loggedUser->getId(), $event->getOrganizer()->getId());
    }

    public function testInvalidOrganizer(): void
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionMessageMatches('/' . preg_quote(User::class) . '/');
        $this->createEvent->create(
            $this->faker->name,
            $this->faker->text,
            EventTypeEnum::INDIVIDUAL_SESSION,
            [Uuid::uuid1()->toString()],
            Uuid::uuid1()->toString(),
            $this->faker->dateTimeBetween('+1 day', '+30 days')->format(DateTimeImmutable::ISO8601),
            $this->eventModel->getId(),
        );
    }

    public function testInvalidModel(): void
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionMessageMatches('/' . preg_quote(EventModel::class) . '/');
        $this->createEvent->create(
            $this->faker->name,
            $this->faker->text,
            EventTypeEnum::INDIVIDUAL_SESSION,
            [$this->loggedUser->getId()],
            $this->loggedUser->getId(),
            $this->faker->dateTimeBetween('+1 day', '+30 days')->format(DateTimeImmutable::ISO8601),
            Uuid::uuid1()->toString(),
        );
    }
}
