<?php

declare(strict_types=1);

namespace App\Tests\Application\Event;

use App\Application\Event\UpdateEventModel;
use App\Domain\Enum\EventTypeEnum;
use App\Domain\Model\EventModel;
use App\Domain\Repository\EventModelRepository;
use App\Tests\Application\ApplicationTestCase;

class UpdateEventModelTest extends ApplicationTestCase
{
    protected UpdateEventModel $updateEventModel;
    protected EventModel $eventModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->updateEventModel = self::$container->get(UpdateEventModel::class);
        $eventModelRepository = self::$container->get(EventModelRepository::class);

        $this->eventModel = new EventModel($this->faker->name, $this->faker->text, EventTypeEnum::INDIVIDUAL_SESSION);
        $eventModelRepository->save($this->eventModel);
    }

    public function testUpdateEventModel(): void
    {
        $name = $this->faker->name;
        $description = $this->faker->text;
        $type = EventTypeEnum::TRIPARTITE;

        $eventModel = $this->updateEventModel->update(
            $this->eventModel->getId(),
            $name,
            $description,
            $type
        );

        $this->assertEquals($name, $eventModel->getName());
        $this->assertEquals($description, $eventModel->getDescription());
        $this->assertEquals($type, $eventModel->getType());
    }
}
