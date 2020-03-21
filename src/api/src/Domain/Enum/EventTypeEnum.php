<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class EventTypeEnum implements StringEnum
{
    public const INDIVIDUAL_SESSION = 'individual_session';
    public const TRIPARTITE = 'tripartite_meeting';
    public const ALLIANCE = 'alliance_session_by_the_coachee';
    public const GROUP_SESSION = 'group_session';
    public const WORKSHOP = 'workshop';
    public const WORKGROUP = 'workgroup';
    public const OUTPLACEMENT = 'outplacement';

    /**
     * @inheritDoc
     */
    public static function values(): array
    {
        return [
            self::INDIVIDUAL_SESSION,
            self::TRIPARTITE,
            self::ALLIANCE,
            self::GROUP_SESSION,
            self::WORKSHOP,
            self::WORKGROUP,
            self::OUTPLACEMENT,
        ];
    }
}
