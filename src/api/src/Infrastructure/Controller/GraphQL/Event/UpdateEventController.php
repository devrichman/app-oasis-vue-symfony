<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Event;

use App\Application\Event\UpdateEvent;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class UpdateEventController extends AbstractController
{
    private UpdateEvent $updateEvent;

    public function __construct(UpdateEvent $updateEvent)
    {
        $this->updateEvent = $updateEvent;
    }

    /**
     * @param string[] $userIds
     *
     * @throws NotFound
     * @throws InvalidDateValue
     * @throws InvalidStringValue
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_CREATE_EVENT")
     */
    public function updateEvent(string $id, string $name, string $description, string $type, array $userIds, ?string $organizerId = null, ?string $dateEvent = null, ?string $modelId = null): Event
    {
        return $this->updateEvent->update($id, $name, $description, $type, $userIds, $organizerId, $dateEvent, $modelId);
    }
}
