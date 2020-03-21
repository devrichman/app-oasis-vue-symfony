<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class TemplateEmailEnum implements StringEnum
{
    public const SITE_OASYS_LINK = 'SITE_OASYS_LINK';
    public const LINKEDIN_LINK = 'LINKEDIN_LINK';
    public const TWITTER_LINK = 'TWITTER_LINK';

    /**
     * @inheritDoc
     */
    public static function values(): array
    {
        return [
            self::SITE_OASYS_LINK,
            self::LINKEDIN_LINK,
            self::TWITTER_LINK,
        ];
    }
}
