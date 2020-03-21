<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class ResetPasswordTokenEnum implements StringEnum
{
    public const SECRET_ENV = 'APP_SECRET';
    public const ALGO = 'HS256';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return [
            self::SECRET_ENV,
            self::ALGO,
        ];
    }
}
