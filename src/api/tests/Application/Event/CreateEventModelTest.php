<?php

declare(strict_types=1);

namespace App\Tests\Application\Event;

use App\Application\Event\CreateEventModel;
use App\Domain\Enum\EventTypeEnum;
use App\Tests\Application\ApplicationTestCase;

class CreateEventModelTest extends ApplicationTestCase
{
    protected CreateEventModel $createEventModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createEventModel = self::$container->get(CreateEventModel::class);
    }

    public function testCreateModelEvent(): void
    {
        $name = $this->faker->name;
        $description = $this->faker->text;
        $type = EventTypeEnum::INDIVIDUAL_SESSION;

        $event = $this->createEventModel->create(
            $name,
            $description,
            $type,
        );

        $this->assertEquals($name, $event->getName());
        $this->assertEquals($description, $event->getDescription());
        $this->assertEquals($type, $event->getType());
    }
}
