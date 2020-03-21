<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Event;

use App\Application\Event\DeleteEventModel;
use App\Domain\Exception\NotFound;
use App\Domain\Exception\UnableDelete;
use App\Domain\Model\EventModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class DeleteEventModelController extends AbstractController
{
    private DeleteEventModel $deleteEventModel;

    public function __construct(DeleteEventModel $deleteEventModel)
    {
        $this->deleteEventModel = $deleteEventModel;
    }

    /**
     * @throws NotFound
     * @throws UnableDelete
     *
     * @Mutation
     * @Logged
     */
    public function deleteEventModel(string $eventModelId): EventModel
    {
        return $this->deleteEventModel->delete($eventModelId);
    }
}
