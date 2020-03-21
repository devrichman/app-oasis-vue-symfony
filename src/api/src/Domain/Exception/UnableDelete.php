<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use App\Domain\Model\EventModel;
use App\Domain\Model\ProgramModel;
use GraphQL\Error\ClientAware;
use RuntimeException;
use function count;
use function Safe\sprintf;

final class UnableDelete extends RuntimeException implements ClientAware, DomainException
{
    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'unable to delete.';
    }

    /**
     * @throws self
     */
    public static function mustNotHaveEvents(EventModel $eventModel): void
    {
        $events = $eventModel->getEvents();
        if (count($events)) {
            throw new self(sprintf('We can not delete this because it has events.'), 400);
        }
    }

    /**
     * @throws self
     */
    public static function mustNotHavePrograms(ProgramModel $programModel): void
    {
        $programs = $programModel->getPrograms();
        if (count($programs)) {
            throw new self(sprintf('We can not delete this because it has programs.'), 400);
        }
    }
}
