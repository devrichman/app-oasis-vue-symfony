<?php

declare(strict_types=1);

namespace App\Application\Document;

use App\Application\Event\AddDocumentToEvent;
use App\Domain\Enum\DocumentEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Document;
use App\Domain\Repository\DocumentRepository;
use App\Domain\Repository\FileDescriptorRepository;
use App\Domain\Repository\UserRepository;
use DateTimeImmutable;

final class CreateDocumentFromEvent
{
    private DocumentRepository $documentRepository;
    private FileDescriptorRepository $fileDescriptorRepository;
    private UserRepository $userRepository;
    private AddDocumentToEvent $addDocumentToEvent;

    public function __construct(FileDescriptorRepository $fileDescriptorRepository, DocumentRepository $documentRepository, UserRepository $userRepository, AddDocumentToEvent $addDocumentToEvent)
    {
        $this->fileDescriptorRepository = $fileDescriptorRepository;
        $this->documentRepository = $documentRepository;
        $this->userRepository = $userRepository;
        $this->addDocumentToEvent = $addDocumentToEvent;
    }

    /**
     * @throws NotFound
     */
    public function create(
        string $name,
        string $description,
        string $tags,
        string $fileDescriptorId,
        string $authorId,
        string $eventId,
        ?DateTimeImmutable $elaborationDate = null
    ): Document {
        $fileDescriptor = $this->fileDescriptorRepository->mustFindOneById($fileDescriptorId);
        $author = $this->userRepository->mustFindOneById($authorId);
        $visibility = DocumentEnum::PROTECTED_CODE;
        $document = new Document($fileDescriptor, $author, $name, $description, $tags, $visibility);

        $document->setElaborationDate($elaborationDate);

        $this->documentRepository->save($document);
        $this->addDocumentToEvent->add($eventId, $document->getId());

        return $document;
    }
}
