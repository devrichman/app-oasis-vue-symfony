<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Event;

use App\Application\Event\CreateEventModel;
use App\Domain\Exception\NotFound;
use App\Domain\Model\EventModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class CreateEventModelController extends AbstractController
{
    private CreateEventModel $createEventModel;

    public function __construct(CreateEventModel $createEventModel)
    {
        $this->createEventModel = $createEventModel;
    }

    /**
     * @throws NotFound
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_CREATE_EVENT")
     */
    public function createEventModel(string $name, string $description, string $type, ?string $programModelId = null): EventModel
    {
        return $this->createEventModel->create($name, $description, $type, $programModelId);
    }
}
