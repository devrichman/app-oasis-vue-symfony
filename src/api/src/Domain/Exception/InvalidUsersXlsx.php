<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use GraphQL\Error\ClientAware;
use RuntimeException;

class InvalidUsersXlsx extends RuntimeException implements ClientAware, DomainException
{
    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'invalid users excel file';
    }

    /**
     * @param mixed[] $errors
     */
    public function __construct(array $errors)
    {
        $message = 'Le fichier Excel téléchargé présente les erreurs suivantes:';
        foreach ($errors as $error) {
            $message .= '<br />' . $error['line'] . '. ' . $error['message'];
        }
        parent::__construct($message, 400, null);
    }
}
