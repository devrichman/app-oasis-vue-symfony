<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Domain\Exception\NotFound;
use App\Domain\Repository\DocumentRepository;
use App\Domain\Repository\EventRepository;

final class AddDocumentToEvent
{
    private DocumentRepository $documentRepository;
    private EventRepository $eventRepository;

    public function __construct(DocumentRepository $documentRepository, EventRepository $eventRepository)
    {
        $this->documentRepository = $documentRepository;
        $this->eventRepository = $eventRepository;
    }

    /**
     * @throws NotFound
     */
    public function add(string $eventId, string $documentId): void
    {
        $document = $this->documentRepository->mustFindOneById($documentId);
        $event = $this->eventRepository->mustFindOneById($eventId);
        $event->addDocument($document);
        $this->eventRepository->save($event);
    }
}
