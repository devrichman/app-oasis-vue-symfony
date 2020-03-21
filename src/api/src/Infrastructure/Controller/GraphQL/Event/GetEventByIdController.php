<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Event;

use App\Application\Event\GetEventById;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class GetEventByIdController extends AbstractController
{
    private GetEventById $getEventById;

    public function __construct(GetEventById $getEventById)
    {
        $this->getEventById = $getEventById;
    }

    /**
     * @throws NotFound
     *
     * @Query
     * @Logged
     */
    public function getEventById(string $eventId): Event
    {
        return $this->getEventById->get($eventId);
    }
}
