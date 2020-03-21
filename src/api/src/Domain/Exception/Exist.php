<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use GraphQL\Error\ClientAware;
use RuntimeException;
use function Safe\sprintf;

final class Exist extends RuntimeException implements ClientAware, DomainException
{
    /**
     * @param array<string,string|null> $properties
     */
    public function __construct(string $model, array $properties, bool $secure = false)
    {
        if ($secure) {
            parent::__construct('cannot proceed', 400, null);

            return;
        }

        $message = sprintf('%s already exists with one or more of those properties:', $model);
        foreach ($properties as $property => $value) {
            if ($value === null) {
                continue;
            }

            $message .= sprintf(' %s = %s', $property, $value);
        }

        parent::__construct($message, 400, null);
    }

    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'exist';
    }
}
