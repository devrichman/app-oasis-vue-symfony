<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use GraphQL\Error\ClientAware;
use RuntimeException;
use function count;
use function get_class;
use function Safe\sprintf;

final class InvalidArrayValue extends RuntimeException implements ClientAware, DomainException
{
    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'invalid array value';
    }

    private static function getProperty(?string $property = null): string
    {
        return $property ? $property : 'value';
    }

    /**
     * @param mixed[] $value
     *
     * @throws self
     */
    public static function notEmpty(array $value, ?string $property = null): void
    {
        if (empty($value) || count($value) === 0) {
            throw new self(sprintf('%s should not be empty', self::getProperty($property)), 400);
        }
    }

    /**
     * @param mixed[] $array
     *
     * @throws self
     */
    public static function valuesOfClass(array $array, string $class, ?string $property = null): void
    {
        foreach ($array as $value) {
            if ($value !== null && get_class($value) !== $class) {
                throw new self(sprintf('%s should have all values of class %s, got %s', self::getProperty($property), $class, get_class($value)));
            }
        }
    }
}
