<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use DateTimeImmutable;
use GraphQL\Error\ClientAware;
use RuntimeException;
use function Safe\sprintf;

final class InvalidDateValue extends RuntimeException implements ClientAware, DomainException
{
    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'invalid date value';
    }

    private static function getProperty(?string $property = null): string
    {
        return $property ? $property : 'value';
    }

    /**
     * @throws self
     */
    public static function isDateSuperior(DateTimeImmutable $superior, ?DateTimeImmutable $inferior, ?string $property = null): void
    {
        if ($superior < $inferior && $inferior !== null) {
            throw new self(sprintf('%s should be a superior to %s: got %s', self::getProperty($property), $inferior->format('Y-m-d H:i:s'), $superior->format('Y-m-d H:i:s')), 400);
        }
    }

    /**
     * @throws self
     */
    public static function invalidDate(?string $property = null): void
    {
        throw new self(sprintf('The date given for %s is in an invalid format, expected valid ISO 8601 format', self::getProperty($property)), 400);
    }
}
