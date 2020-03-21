<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Repository\DocumentRepository;
use TheCodingMachine\TDBM\ResultIterator;

final class GetAllDocuments
{
    private DocumentRepository $documentRepository;

    public function __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    public function getAll(?string $search, ?string $visibility = null, ?string $sortColumn = 'createdAt', ?string $sortDirection = 'desc'): ResultIterator
    {
        return $this->documentRepository->findByFilters($search, $visibility, $sortColumn, $sortDirection);
    }
}
