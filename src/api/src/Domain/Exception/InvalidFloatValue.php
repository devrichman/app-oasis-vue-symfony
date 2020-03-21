<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use GraphQL\Error\ClientAware;
use RuntimeException;
use function Safe\preg_match;
use function Safe\sprintf;

final class InvalidFloatValue extends RuntimeException implements ClientAware, DomainException
{
    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'invalid float value';
    }

    private static function getProperty(?string $property = null): string
    {
        return $property ? $property : 'value';
    }

    /**
     * @throws self
     */
    public static function between(float $value, float $min = 0.0, ?float $max = null, ?string $property = null): void
    {
        if ($value < $min ||
            ($max !== null && $value > $max)) {
            if ($max === null) {
                throw new self(sprintf('%s should be > %f: got %d', self::getProperty($property), $min, $value), 400);
            }

            throw new self(sprintf('%s should be between %f and %f: got %d', self::getProperty($property), $min, $max, $value), 400);
        }
    }

    /**
     * @throws self
     */
    public static function isCoordinateValid(float $latitude, float $longitude, ?string $property = null): void
    {
        $regex = '/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?),\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/';
        $coordinates = $latitude . ',' . $longitude;

        if (! preg_match($regex, $coordinates)) {
            throw new self(sprintf('%s should be a valid location coordinate: got %s', self::getProperty($property), $coordinates), 400);
        }
    }
}
