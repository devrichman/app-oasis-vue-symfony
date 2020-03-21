<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use RuntimeException;

class InvalidImportUser extends RuntimeException
{
    /** @var string[] $errors */
    protected array $errors;

    /**
     * @param string[] $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct();
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
