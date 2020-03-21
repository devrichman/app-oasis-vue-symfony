<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Document;
use App\Domain\Repository\DocumentRepository;

final class DeleteDocument
{
    private DocumentRepository $documentRepository;

    public function __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    /**
     * @throws NotFound
     */
    public function delete(string $id): Document
    {
        $document = $this->documentRepository->mustFindOneById($id);
        $document->setDeleted(true);

        $this->documentRepository->save($document);

        return $document;
    }
}
