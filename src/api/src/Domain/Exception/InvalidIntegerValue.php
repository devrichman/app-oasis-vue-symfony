<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use GraphQL\Error\ClientAware;
use RuntimeException;
use function Safe\sprintf;

final class InvalidIntegerValue extends RuntimeException implements ClientAware, DomainException
{
    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'invalid integer value';
    }

    private static function getProperty(?string $property = null): string
    {
        return $property ? $property : 'value';
    }

    /**
     * @throws self
     */
    public static function between(int $value, int $min = 0, ?int $max = null, ?string $property = null): void
    {
        if ($value < $min ||
            ($max !== null && $value > $max)) {
            if ($max === null) {
                throw new self(sprintf('%s should be > %d: got %d', self::getProperty($property), $min, $value), 400);
            }

            throw new self(sprintf('%s should be between %d and %d: got %d', self::getProperty($property), $min, $max, $value), 400);
        }
    }
}
