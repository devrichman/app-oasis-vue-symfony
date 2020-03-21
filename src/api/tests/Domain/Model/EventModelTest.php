<?php

declare(strict_types=1);

namespace App\Tests\Domain\Model;

use App\Domain\Enum\EventTypeEnum;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Model\EventModel;
use App\Tests\Domain\DomainTestCase;

final class EventModelTest extends DomainTestCase
{
    public function testSetEventName(): void
    {
        $event = new EventModel($this->faker->name, $this->faker->text, EventTypeEnum::INDIVIDUAL_SESSION);

        $name = $this->faker->name;
        $event->setName($name);
        $this->assertEquals($name, $event->getName());

        $this->expectException(InvalidStringValue::class);
        $event->setName('');

        $this->expectException(InvalidStringValue::class);
        $event->setName($this->faker->text(256));
    }

    public function testSetEventDescription(): void
    {
        $event = new EventModel($this->faker->name, $this->faker->text, EventTypeEnum::INDIVIDUAL_SESSION);

        $description = $this->faker->text;
        $event->setDescription($description);
        $this->assertEquals($description, $event->getDescription());

        $this->expectException(InvalidStringValue::class);
        $event->setDescription('');
    }
}
