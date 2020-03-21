<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class UserTypeEnum implements StringEnum
{
    public const ADMINISTRATOR = 'admin';
    public const CANDIDATE = 'candidate';
    public const COACH = 'coach';
    public const SUPPORT = 'support';

    public const ADMINISTRATOR_LABEL = 'Administrateur';
    public const CANDIDATE_LABEL = 'Candidat';
    public const COACH_LABEL = 'Coach-Consultant';
    public const SUPPORT_LABEL = 'Support-Admin';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return [
            self::ADMINISTRATOR_LABEL => self::ADMINISTRATOR,
            self::CANDIDATE_LABEL => self::CANDIDATE,
            self::COACH_LABEL => self::COACH,
            self::SUPPORT_LABEL => self::SUPPORT,
        ];
    }
}
