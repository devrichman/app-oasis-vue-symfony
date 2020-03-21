<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class ProgramTypeEnum implements StringEnum
{
    public const INDIVIDUAL = 'individual';
    public const GROUP = 'group';

    /**
     * @inheritDoc
     */
    public static function values(): array
    {
        return [
            self::INDIVIDUAL,
            self::GROUP,
        ];
    }
}
