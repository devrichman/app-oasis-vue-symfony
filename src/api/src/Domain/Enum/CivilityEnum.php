<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class CivilityEnum implements StringEnum
{
    public const MISTER_CODE = 'm';
    public const MADAM_CODE = 'mme';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return [
            self::MISTER_CODE,
            self::MADAM_CODE,
        ];
    }
}
