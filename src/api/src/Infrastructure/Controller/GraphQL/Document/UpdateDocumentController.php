<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Document;

use App\Application\Document\UpdateDocument;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Document;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class UpdateDocumentController extends AbstractController
{
    private UpdateDocument $updateDocument;

    public function __construct(UpdateDocument $updateDocument)
    {
        $this->updateDocument = $updateDocument;
    }

    /**
     * @throws NotFound
     * @throws InvalidStringValue
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_UPDATE_DOCUMENT")
     */
    public function updateDocument(
        string $id,
        string $name,
        string $description,
        string $tags,
        string $visibility,
        string $fileDescriptorId,
        string $authorId,
        ?DateTimeImmutable $elaborationDate = null
    ): Document {
        return $this->updateDocument->update(
            $id,
            $name,
            $description,
            $tags,
            $visibility,
            $fileDescriptorId,
            $authorId,
            $elaborationDate
        );
    }
}
