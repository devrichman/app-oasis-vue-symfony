<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Document;

use App\Application\Document\DeleteDocument;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Document;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class DeleteDocumentController extends AbstractController
{
    private DeleteDocument $deleteDocument;

    public function __construct(DeleteDocument $deleteDocument)
    {
        $this->deleteDocument = $deleteDocument;
    }

    /**
     * @throws NotFound
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_DELETE_DOCUMENT")
     */
    public function deleteDocument(
        string $id
    ): Document {
        return $this->deleteDocument->delete($id);
    }
}
