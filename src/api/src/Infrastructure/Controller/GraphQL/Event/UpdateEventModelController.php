<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Event;

use App\Application\Event\UpdateEventModel;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\EventModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class UpdateEventModelController extends AbstractController
{
    private UpdateEventModel $updateEventModel;

    public function __construct(UpdateEventModel $updateEventModel)
    {
        $this->updateEventModel = $updateEventModel;
    }

    /**
     * @throws InvalidStringValue
     * @throws NotFound
     *
     * @Mutation
     * @Logged
     */
    public function updateEventModel(string $id, string $name, string $description, string $type): EventModel
    {
        return $this->updateEventModel->update($id, $name, $description, $type);
    }
}
