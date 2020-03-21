<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use GraphQL\Error\ClientAware;
use RuntimeException;

final class InvalidRight extends RuntimeException implements ClientAware, DomainException
{
    public function __construct()
    {
        $message = "Can't create admin without being admin";

        parent::__construct($message, 400, null);
    }

    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'invalid right';
    }
}
