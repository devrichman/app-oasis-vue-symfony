<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Document;

use App\Application\Document\CreateDocumentFromEvent;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Document;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class CreateDocumentFromEventController extends AbstractController
{
    private CreateDocumentFromEvent $createDocumentFromEvent;

    public function __construct(CreateDocumentFromEvent $createDocumentFromEvent)
    {
        $this->createDocumentFromEvent = $createDocumentFromEvent;
    }

    /**
     * @throws NotFound
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_CREATE_DOCUMENT")
     */
    public function createDocumentFromEvent(
        string $name,
        string $description,
        string $tags,
        string $fileDescriptorId,
        string $authorId,
        string $eventId,
        ?DateTimeImmutable $elaborationDate = null
    ): Document {
        return $this->createDocumentFromEvent->create(
            $name,
            $description,
            $tags,
            $fileDescriptorId,
            $authorId,
            $eventId,
            $elaborationDate
        );
    }
}
