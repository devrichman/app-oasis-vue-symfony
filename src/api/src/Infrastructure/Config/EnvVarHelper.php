<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

use Symfony\Component\DependencyInjection\Exception\EnvNotFoundException;
use function getenv;
use function Safe\sprintf;

final class EnvVarHelper
{
    public function fetch(string $envVar): string
    {
        $value = getenv($envVar);
        if ($value === false) {
            throw new EnvNotFoundException(sprintf('environment variable %s does not exist', $envVar));
        }

        return $value;
    }
}
