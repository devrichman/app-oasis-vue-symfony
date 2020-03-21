<?php

declare(strict_types=1);

namespace App\Tests\Application\Event;

use App\Application\Event\DeleteEventModel;
use App\Domain\Enum\EventTypeEnum;
use App\Domain\Model\EventModel;
use App\Domain\Repository\EventModelRepository;
use App\Tests\Application\ApplicationTestCase;

class DeleteEventModelTest extends ApplicationTestCase
{
    protected DeleteEventModel $deleteEventModel;
    protected EventModel $eventModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->deleteEventModel = self::$container->get(DeleteEventModel::class);
        $eventModelRepository = self::$container->get(EventModelRepository::class);

        $this->eventModel = new EventModel($this->faker->name, $this->faker->text, EventTypeEnum::INDIVIDUAL_SESSION);
        $eventModelRepository->save($this->eventModel);
    }

    public function testDeleteEventModel(): void
    {
        $eventModelId = $this->eventModel->getId();
        $this->deleteEventModel->delete($eventModelId);
        $this->assertTrue($this->eventModel->getDeleted());
    }
}
