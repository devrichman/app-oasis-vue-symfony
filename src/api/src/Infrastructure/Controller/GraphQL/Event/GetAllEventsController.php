<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Event;

use App\Application\Event\GetAllEvents;
use App\Domain\Model\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllEventsController extends AbstractController
{
    private GetAllEvents $getAllEvents;

    public function __construct(GetAllEvents $getAllEvents)
    {
        $this->getAllEvents = $getAllEvents;
    }

    /**
     * @return ResultIterator|Event[]
     *
     * @Query
     * @Logged
     */
    public function getAllEvents(?string $search = null, ?string $status = null, string $sortColumn = 'createdAt', string $sortDirection = 'desc'): ResultIterator
    {
        /** @var ResultIterator|Event[] $result */
        $result = $this->getAllEvents->getAll($search, $status, $sortColumn, $sortDirection);

        return $result;
    }
}
