<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Document;
use App\Domain\Repository\DocumentRepository;
use App\Domain\Repository\FileDescriptorRepository;
use App\Domain\Repository\UserRepository;
use DateTimeImmutable;

final class UpdateDocument
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
     * @throws InvalidStringValue
     */
    public function update(
        string $id,
        string $name,
        string $description,
        string $tags,
        string $visibility,
        string $fileDescriptorId,
        string $authorId,
        ?DateTimeImmutable $elaborationDate = null
    ): Document {
        $document = $this->documentRepository->mustFindOneById($id);

        $fileDescriptor = $this->fileDescriptorRepository->mustFindOneById($fileDescriptorId);
        if ($fileDescriptor !== $document->getFileDescriptor()) {
            $oldFile = $document->getFileDescriptor();
        }

        $author = $this->userRepository->mustFindOneById($authorId);

        $document->setVisibility($visibility);
        $document->setName($name);
        $document->setElaborationDate($elaborationDate);
        $document->setFileDescriptor($fileDescriptor);
        $document->setDescription($description);
        $document->setAuthor($author);
        $document->setTags($tags);

        $this->documentRepository->save($document);

        if (isset($oldFile)) {
            $this->fileDescriptorRepository->delete($oldFile);
        }

        return $document;
    }
}
