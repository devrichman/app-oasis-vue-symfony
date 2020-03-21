<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Event;

use App\Application\Event\GetEventModelById;
use App\Domain\Exception\NotFound;
use App\Domain\Model\EventModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class GetEventModelByIdController extends AbstractController
{
    private GetEventModelById $getEventModelById;

    public function __construct(GetEventModelById $getEventModelById)
    {
        $this->getEventModelById = $getEventModelById;
    }

    /**
     * @throws NotFound
     *
     * @Query
     * @Logged
     */
    public function getEventModelById(string $eventModelId): EventModel
    {
        return $this->getEventModelById->get($eventModelId);
    }
}
