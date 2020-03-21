<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class HostEnum implements StringEnum
{
    public const HOST_URL = 'HOST_URL';
    public const HOST_PROTOCOL = 'HOST_PROTOCOL';

    /**
     * @inheritDoc
     */
    public static function values(): array
    {
        return [
            self::HOST_URL,
            self::HOST_PROTOCOL,
        ];
    }
}
