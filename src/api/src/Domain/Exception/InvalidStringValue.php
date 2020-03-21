<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use App\Domain\Enum\CivilityEnum;
use App\Domain\Enum\DocumentEnum;
use App\Domain\Enum\EventTypeEnum;
use GraphQL\Error\ClientAware;
use RuntimeException;
use Throwable;
use function filter_var;
use function implode;
use function in_array;
use function Safe\preg_match;
use function Safe\sprintf;
use function str_replace;
use function strlen;
use const FILTER_VALIDATE_EMAIL;

final class InvalidStringValue extends RuntimeException implements ClientAware, DomainException
{
    protected string $property;

    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null, string $property = '')
    {
        $this->property = $property;
        parent::__construct($message, $code, $previous);
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'invalid string value';
    }

    private static function getPropertyValue(?string $property = null): string
    {
        return $property ? $property : 'value';
    }

    /**
     * @throws self
     */
    public static function notBlank(?string $value, ?string $property = null): void
    {
        if (empty($value) || $value === '') {
            throw new self(sprintf('%s should not be blank: got %s', self::getPropertyValue($property), $value), 400, null, self::getPropertyValue($property));
        }
    }

    /**
     * @throws self
     */
    public static function eventType(?string $value, ?string $property = null): void
    {
        if (! in_array($value, EventTypeEnum::values())) {
            throw new InvalidStringValue(sprintf('The type %s is not one of the valid types', $value), 400);
        }
    }

    /**
     * @throws self
     */
    public static function length(string $value, int $min = 0, ?int $max = null, ?string $property = null): void
    {
        if (strlen($value) < $min ||
            ($max !== null && strlen($value) > $max)) {
            if ($max === null) {
                throw new self(sprintf('%s should have a length > %d: got %s', self::getPropertyValue($property), $min, $value), 400, null, self::getPropertyValue($property));
            }

            throw new self(sprintf('%s should be between %d and %d: got %s', self::getPropertyValue($property), $min, $max, $value), 400, null, self::getPropertyValue($property));
        }
    }

    /**
     * @param string[] $values

     * @throws self
     */
    public static function isOneOf(string $value, array $values, ?string $property = null): void
    {
        if (! in_array($value, $values)) {
            throw new self(sprintf('%s should be one of %s: got %s', self::getPropertyValue($property), implode(', ', $values), $value), 400, null, self::getPropertyValue($property));
        }
    }

    /**
     * @throws self
     */
    public static function email(string $value, ?string $property = null): void
    {
        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new self(sprintf('%s should be a valid e-mail address: got %s', self::getPropertyValue($property), $value), 400, null, self::getPropertyValue($property));
        }
    }

    /**
     * @throws self
     */
    public static function password(string $value, ?string $property): void
    {
        $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/';
        if (! preg_match($regex, $value)) {
            throw new self(sprintf('%s should contain 8 characters at least with uppercase, lowercase letters and a number: got %s', self::getPropertyValue($property), $value), 400, null, self::getPropertyValue($property));
        }
    }

    /**
     * @throws self
     */
    public static function siret(string $value, ?string $property): void
    {
        $regex = '/^[0-9]{3}[ \.\-]?[0-9]{3}[ \.\-]?[0-9]{3}[ \.\-]?[0-9]{5}$/';
        if (! preg_match($regex, $value)) {
            throw new self(sprintf('%s should be a valid siret: got %s', self::getPropertyValue($property), $value), 400, null, self::getPropertyValue($property));
        }
    }

    /**
     * @throws self
     */
    public static function siren(string $value, ?string $property): void
    {
        $regex = '/^[0-9]{3}[ \.\-]?[0-9]{3}[ \.\-]?[0-9]{3}$/';
        if (! preg_match($regex, $value)) {
            throw new self(sprintf('%s should be a valid siren: got %s', self::getPropertyValue($property), $value), 400, null, self::getPropertyValue($property));
        }
    }

    /**
     * @throws self
     */
    public static function phone(?string $value, ?string $property): void
    {
        if (empty($value)) {
            return;
        }
        $regex = '/^(?:(?:\+|00)33[\s.-]{0,3}(?:\(0\)[\s.-]{0,3})?|0)?[1-9](?:(?:[\s.-]?\d{2}){4}|\d{2}(?:[\s.-]?\d{3}){2})$/';
        if (! preg_match($regex, str_replace(' ', '', $value))) {
            throw new self(sprintf('%s should be a valid phone number, got %s', self::getPropertyValue($property), $value), 400, null, self::getPropertyValue($property));
        }
    }

    /**
     * @throws self
     */
    public static function civility(?string $value, ?string $property): void
    {
        if (! ($value === CivilityEnum::MISTER_CODE || $value === CivilityEnum::MADAM_CODE)) {
            throw new self(sprintf('%s should be a valid civility, got %s', self::getPropertyValue($property), $value), 400, null, self::getPropertyValue($property));
        }
    }

    /**
     * @throws self
     */
    public static function visibility(?string $value, ?string $property): void
    {
        if (! ($value === DocumentEnum::PRIVATE_CODE || $value === DocumentEnum::PUBLIC_CODE || $value === DocumentEnum::PROTECTED_CODE)) {
            throw new self(sprintf('%s should be a valid visibility, got %s', self::getPropertyValue($property), $value), 400, null, self::getPropertyValue($property));
        }
    }
}
