<?php
/*
 * This file has been automatically generated by TDBM.
 * You can edit this file as it will not be overwritten.
 */

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Logging\LoggableModel;
use App\Domain\Model\Generated\AbstractEvent;
use DateTimeImmutable as UnsafeDateTimeImmutable;
use Safe\DateTimeImmutable;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * The Event class maps the 'events' table in database.
 *
 * @Type
 */
class Event extends AbstractEvent implements LoggableModel
{
    /**
     * @throws InvalidStringValue
     */
    public function setName(string $name): void
    {
        $property = 'name';
        InvalidStringValue::notBlank($name, $property);
        InvalidStringValue::length($name, 1, 255, $property);
        parent::setName($name);
    }

    /**
     * @throws InvalidStringValue
     */
    public function setDescription(string $description): void
    {
        $property = 'description';
        InvalidStringValue::notBlank($description, $property);
        parent::setDescription($description);
    }

    /**
     * @throws InvalidStringValue
     */
    public function setType(string $type): void
    {
        $property = 'type';
        InvalidStringValue::notBlank($type, $property);
        InvalidStringValue::eventType($type, $property);
        parent::setType($type);
    }

    /**
     * @throws InvalidDateValue
     */
    public function setDateEvent(?UnsafeDateTimeImmutable $dateEvent): void
    {
        if ($dateEvent) {
            $property = 'dateEvent';
            InvalidDateValue::isDateSuperior($dateEvent, new DateTimeImmutable(), $property);
        }
        parent::setDateEvent($dateEvent);
    }
}
