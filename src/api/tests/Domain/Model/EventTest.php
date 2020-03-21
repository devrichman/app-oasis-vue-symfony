<?php

declare(strict_types=1);

namespace App\Tests\Domain\Model;

use App\Domain\Enum\EventTypeEnum;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Model\Event;
use App\Tests\Domain\DomainTestCase;
use Safe\DateTimeImmutable;

final class EventTest extends DomainTestCase
{
    public function testSetEventName(): void
    {
        $event = new Event($this->faker->name, $this->faker->text, EventTypeEnum::INDIVIDUAL_SESSION);

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
        $event = new Event($this->faker->name, $this->faker->text, EventTypeEnum::INDIVIDUAL_SESSION);

        $description = $this->faker->text;
        $event->setDescription($description);
        $this->assertEquals($description, $event->getDescription());

        $this->expectException(InvalidStringValue::class);
        $event->setDescription('');
    }

    public function testSetEventDate(): void
    {
        $event = new Event($this->faker->name, $this->faker->text, EventTypeEnum::INDIVIDUAL_SESSION);
        $dateEvent = $this->faker->dateTimeBetween('now', '+30 days');

        $event->setDateEvent(\DateTimeImmutable::createFromFormat(DateTimeImmutable::ISO8601, $dateEvent->format('c')));
        $this->assertEquals($dateEvent->getTimestamp(), $event->getDateEvent()->getTimestamp());

        $this->expectException(InvalidDateValue::class);
        $dateEvent = $this->faker->dateTimeBetween('-30 days', '-1 day');
        $event->setDateEvent(\DateTimeImmutable::createFromFormat(DateTimeImmutable::ISO8601, $dateEvent->format('c')));
    }
}
