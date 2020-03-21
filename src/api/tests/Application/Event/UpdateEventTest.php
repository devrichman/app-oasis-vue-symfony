<?php

declare(strict_types=1);

namespace App\Tests\Application\Event;

use App\Application\Event\UpdateEvent;
use App\Domain\Enum\EventTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Model\EventModel;
use App\Domain\Model\User;
use App\Domain\Repository\EventModelRepository;
use App\Domain\Repository\EventRepository;
use App\Tests\Application\ApplicationTestCase;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use function preg_quote;

class UpdateEventTest extends ApplicationTestCase
{
    protected UpdateEvent $updateEvent;
    protected EventModel $eventModel;
    protected Event $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->updateEvent = self::$container->get(UpdateEvent::class);
        $eventRepository = self::$container->get(EventRepository::class);
        $eventModelRepository = self::$container->get(EventModelRepository::class);

        $eventModel = new EventModel($this->faker->name, $this->faker->text, EventTypeEnum::INDIVIDUAL_SESSION);
        $eventModelRepository->save($eventModel);

        $this->event = new Event($this->faker->name, $this->faker->text, EventTypeEnum::INDIVIDUAL_SESSION);
        $this->event->setEventModel($eventModel);
        $this->event->setOrganizer($this->loggedUser);
        $this->event->setDateEvent(DateTimeImmutable::createFromFormat(DateTimeImmutable::ISO8601, $this->faker->dateTimeBetween('+1 day', '+30 days')->format('c')));
        $eventRepository->save($this->event);

        $this->eventModel = new EventModel($this->faker->name, $this->faker->text, EventTypeEnum::INDIVIDUAL_SESSION);
        $eventModelRepository->save($this->eventModel);
    }

    public function testUpdateEvent(): void
    {
        $name = $this->faker->name;
        $description = $this->faker->text;
        $type = EventTypeEnum::INDIVIDUAL_SESSION;
        $dateEvent = $this->faker->dateTimeBetween('+1 day', '+30 days');

        $event = $this->updateEvent->update(
            $this->event->getId(),
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
        $this->updateEvent->update(
            $this->event->getId(),
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
        $this->updateEvent->update(
            $this->event->getId(),
            $this->faker->name,
            $this->faker->text,
            EventTypeEnum::INDIVIDUAL_SESSION,
            [$this->loggedUser->getId()],
            $this->loggedUser->getId(),
            $this->faker->dateTimeBetween('+1 day', '+30 days')->format(DateTimeImmutable::ISO8601),
            Uuid::uuid1()->toString(),
        );
    }

    public function testInvalidEvent(): void
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionMessageMatches('/' . preg_quote(Event::class) . '/');
        $this->updateEvent->update(
            Uuid::uuid1()->toString(),
            $this->faker->name,
            $this->faker->text,
            EventTypeEnum::INDIVIDUAL_SESSION,
            [$this->loggedUser->getId()],
            $this->loggedUser->getId(),
            $this->faker->dateTimeBetween('+1 day', '+30 days')->format(DateTimeImmutable::ISO8601),
            $this->eventModel->getId(),
        );
    }
}
