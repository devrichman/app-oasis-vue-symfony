<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Document;
use TheCodingMachine\TDBM\ResultIterator;

interface DocumentRepository
{
    public function save(Document $company): void;

    /**
     * @throws NotFound
     */
    public function mustFindOneById(string $id): Document;

    /**
     * @return Document[]|ResultIterator
     */
    public function findByFilters(?string $search, ?string $visibility, ?string $sortColumn, ?string $sortDirection): ResultIterator;

    public function delete(Document $document): void;
}
