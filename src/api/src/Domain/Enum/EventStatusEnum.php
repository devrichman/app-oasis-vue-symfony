<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class EventStatusEnum implements StringEnum
{
    public const CREATED = 'created';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return [
            self::CREATED,
        ];
    }
}
