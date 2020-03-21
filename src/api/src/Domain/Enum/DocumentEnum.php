<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class DocumentEnum implements StringEnum
{
    public const PUBLIC_CODE = 'public';
    public const PROTECTED_CODE = 'protected';
    public const PRIVATE_CODE = 'private';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return [
            self::PUBLIC_CODE,
            self::PROTECTED_CODE,
            self::PRIVATE_CODE,
        ];
    }
}
