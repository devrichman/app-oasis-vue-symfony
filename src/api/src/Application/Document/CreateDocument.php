<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Exception\NotFound;
use App\Domain\Model\Document;
use App\Domain\Repository\DocumentRepository;
use App\Domain\Repository\FileDescriptorRepository;
use App\Domain\Repository\UserRepository;
use DateTimeImmutable;

final class CreateDocument
{
    private DocumentRepository $documentRepository;
    private FileDescriptorRepository $fileDescriptorRepository;
    private UserRepository $userRepository;

    public function __construct(FileDescriptorRepository $fileDescriptorRepository, DocumentRepository $documentRepository, UserRepository $userRepository)
    {
        $this->fileDescriptorRepository = $fileDescriptorRepository;
        $this->documentRepository = $documentRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws NotFound
     */
    public function create(
        string $name,
        string $description,
        string $tags,
        string $visibility,
        string $fileDescriptorId,
        string $authorId,
        ?DateTimeImmutable $elaborationDate = null
    ): Document {
        $fileDescriptor = $this->fileDescriptorRepository->mustFindOneById($fileDescriptorId);
        $author = $this->userRepository->mustFindOneById($authorId);
        $document = new Document($fileDescriptor, $author, $name, $description, $tags, $visibility);
        $document->setElaborationDate($elaborationDate);
        $this->documentRepository->save($document);

        return $document;
    }
}
