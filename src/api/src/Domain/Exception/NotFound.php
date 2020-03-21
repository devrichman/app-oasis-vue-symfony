<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use GraphQL\Error\ClientAware;
use RuntimeException;
use Throwable;
use function Safe\sprintf;

final class NotFound extends RuntimeException implements ClientAware, DomainException
{
    protected string $model;
    /** @var array<string,string|null> $properties */
    protected array $properties;

    /**
     * @param array<string,string|null> $properties
     */
    public function __construct(string $model, array $properties, ?Throwable $e = null)
    {
        $this->model = $model;
        $this->properties = $properties;

        $message = sprintf('%s has not been found with one or more of those properties:', $model);
        foreach ($properties as $property => $value) {
            if ($value === null) {
                continue;
            }

            $message .= sprintf(' %s = %s', $property, $value);
        }

        parent::__construct($message, 404, $e);
    }

    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'not found';
    }

    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @return array<string,string|null>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}
