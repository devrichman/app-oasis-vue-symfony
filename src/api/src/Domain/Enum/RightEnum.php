<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class RightEnum implements StringEnum
{
    public const CREATE_ROLE_CODE = 'ROLE_CREATE_ROLE';
    public const UPDATE_ROLE_CODE = 'ROLE_UPDATE_ROLE';
    public const DELETE_ROLE_CODE = 'ROLE_DELETE_ROLE';

    public const CREATE_USER_CODE = 'ROLE_CREATE_USER';
    public const UPDATE_USER_CODE = 'ROLE_UPDATE_USER';
    public const DELETE_USER_CODE = 'ROLE_DELETE_USER';
    public const DISABLE_USER_CODE = 'ROLE_DISABLE_USER';
    public const ACCESS_USER_CODE = 'ROLE_ACCESS_USER_MENU';

    public const CREATE_COMPANY_CODE = 'ROLE_CREATE_COMPANY';
    public const UPDATE_COMPANY_CODE = 'ROLE_UPDATE_COMPANY';
    public const DELETE_COMPANY_CODE = 'ROLE_DELETE_COMPANY';
    public const ACCESS_COMPANY_CODE = 'ROLE_ACCESS_COMPANY_MENU';

    public const CREATE_EVENT_CODE = 'ROLE_CREATE_EVENT';
    public const UPDATE_EVENT_CODE = 'ROLE_UPDATE_EVENT';
    public const ACCESS_EVENT_CODE = 'ROLE_ACCESS_EVENT';
    public const DELETE_EVENT_CODE = 'ROLE_DELETE_EVENT';

    public const ACCESS_PROGRAM_CODE = 'ROLE_ACCESS_PROGRAM';
    public const CREATE_PROGRAM_CODE = 'ROLE_CREATE_PROGRAM';
    public const DELETE_PROGRAM_CODE = 'ROLE_DELETE_PROGRAM';
    public const UPDATE_PROGRAM_CODE = 'ROLE_UPDATE_PROGRAM';

    public const CREATE_DOCUMENT_CODE = 'ROLE_CREATE_DOCUMENT';
    public const UPDATE_DOCUMENT_CODE = 'ROLE_UPDATE_DOCUMENT';
    public const DELETE_DOCUMENT_CODE = 'ROLE_DELETE_DOCUMENT';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return [
            self::CREATE_ROLE_CODE,
            self::UPDATE_ROLE_CODE,
            self::DELETE_ROLE_CODE,

            self::CREATE_USER_CODE,
            self::UPDATE_USER_CODE,
            self::DELETE_USER_CODE,
            self::DISABLE_USER_CODE,
            self::ACCESS_USER_CODE,

            self::CREATE_COMPANY_CODE,
            self::UPDATE_COMPANY_CODE,
            self::DELETE_COMPANY_CODE,
            self::ACCESS_COMPANY_CODE,

            self::CREATE_EVENT_CODE,
            self::UPDATE_EVENT_CODE,
            self::ACCESS_EVENT_CODE,

            self::ACCESS_PROGRAM_CODE,
            self::CREATE_PROGRAM_CODE,
            self::UPDATE_PROGRAM_CODE,

            self::CREATE_DOCUMENT_CODE,
            self::UPDATE_DOCUMENT_CODE,
            self::DELETE_DOCUMENT_CODE,
        ];
    }
}
