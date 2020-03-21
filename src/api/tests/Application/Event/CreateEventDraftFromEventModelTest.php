<?php

declare(strict_types=1);

namespace App\Tests\Application\Event;

use App\Application\Event\CreateEventDraftFromEventModel;
use App\Domain\Enum\EventTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\EventModel;
use App\Domain\Model\User;
use App\Domain\Repository\EventModelRepository;
use App\Tests\Application\ApplicationTestCase;
use Ramsey\Uuid\Uuid;
use function preg_quote;

class CreateEventDraftFromEventModelTest extends ApplicationTestCase
{
    protected CreateEventDraftFromEventModel $createEventDraftFromEventModel;
    protected EventModel $eventModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createEventDraftFromEventModel = self::$container->get(CreateEventDraftFromEventModel::class);

        $this->eventModel = new EventModel($this->faker->name, $this->faker->text, EventTypeEnum::INDIVIDUAL_SESSION);
        self::$container->get(EventModelRepository::class)->save($this->eventModel);
    }

    public function testCreateEventFromEventModel(): void
    {
        $event = $this->createEventDraftFromEventModel->create(
            $this->eventModel->getId(),
            [$this->loggedUser->getId()],
        );

        $this->assertEquals($this->eventModel->getName(), $event->getName());
        $this->assertEquals($this->eventModel->getDescription(), $event->getDescription());
    }

    public function testInvalidOrganizer(): void
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionMessageMatches('/' . preg_quote(User::class) . '/');
        $this->createEventDraftFromEventModel->create(
            $this->eventModel->getId(),
            [Uuid::uuid1()->toString()],
        );
    }

    public function testInvalidModel(): void
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionMessageMatches('/' . preg_quote(EventModel::class) . '/');
        $this->createEventDraftFromEventModel->create(
            Uuid::uuid1()->toString(),
            [$this->loggedUser->getId()],
        );
    }
}
