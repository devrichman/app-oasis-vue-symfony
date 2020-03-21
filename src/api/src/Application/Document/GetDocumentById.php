<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Document;
use App\Domain\Repository\DocumentRepository;

final class GetDocumentById
{
    private DocumentRepository $documentRepository;

    public function __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    /**
     * @throws NotFound
     */
    public function get(string $id): Document
    {
        return $this->documentRepository->mustFindOneById($id);
    }
}
