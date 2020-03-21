<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Document;

use App\Application\Document\CreateDocument;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Document;
use Safe\DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class CreateDocumentController extends AbstractController
{
    private CreateDocument $createDocument;

    public function __construct(CreateDocument $createDocument)
    {
        $this->createDocument = $createDocument;
    }

    /**
     * @throws NotFound
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_CREATE_DOCUMENT")
     */
    public function createDocument(
        string $name,
        string $description,
        string $tags,
        string $visibility,
        string $fileDescriptorId,
        string $authorId,
        ?string $elaborationDate = null
    ): Document {
        return $this->createDocument->create(
            $name,
            $description,
            $tags,
            $visibility,
            $fileDescriptorId,
            $authorId,
            $elaborationDate !== null ? new DateTimeImmutable($elaborationDate) : null
        );
    }
}
