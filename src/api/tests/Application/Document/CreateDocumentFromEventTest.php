<?php

declare(strict_types=1);

namespace App\Tests\Application\Document;

use App\Application\Document\CreateDocumentFromEvent;
use App\Domain\Enum\DocumentEnum;
use App\Domain\Enum\EventTypeEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Model\Event;
use App\Domain\Model\EventModel;
use App\Domain\Model\FileDescriptor;
use App\Domain\Model\User;
use App\Domain\Repository\EventModelRepository;
use App\Domain\Repository\EventRepository;
use App\Domain\Repository\FileDescriptorRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use App\Tests\Application\ApplicationTestCase;
use DateTimeImmutable;
use Faker\Factory;
use Ramsey\Uuid\Uuid;
use function preg_quote;

class CreateDocumentFromEventTest extends ApplicationTestCase
{
    protected CreateDocumentFromEvent $createDocumentFromEvent;
    protected FileDescriptor $file;
    protected User $author;
    protected Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        $userType = self::$container->get(UserTypeRepository::class)->mustFindOneById(UserTypeEnum::COACH);
        $this->createDocumentFromEvent = self::$container->get(CreateDocumentFromEvent::class);
        $this->file = new FileDescriptor($this->faker->name, $this->faker->numberBetween(1500, 25000), $this->faker->name);
        $this->author = new User($userType, $this->faker->firstName, $this->faker->lastName, $this->faker->email, $this->faker->phoneNumber);
        self::$container->get(UserRepository::class)->save($this->author);
        self::$container->get(FileDescriptorRepository::class)->save($this->file);

        $eventRepository = self::$container->get(EventRepository::class);
        $eventModelRepository = self::$container->get(EventModelRepository::class);
        $eventModel = new EventModel($this->faker->name, $this->faker->text, EventTypeEnum::INDIVIDUAL_SESSION);
        $eventModelRepository->save($eventModel);
        $this->event = new Event($this->faker->name, $this->faker->text, EventTypeEnum::INDIVIDUAL_SESSION);
        $this->event->setEventModel($eventModel);
        $this->event->setOrganizer($this->loggedUser);
        $this->event->setDateEvent(DateTimeImmutable::createFromFormat(DateTimeImmutable::ISO8601, $this->faker->dateTimeBetween('+1 day', '+30 days')->format('c')));
        $eventRepository->save($this->event);
    }

    /**
     * @dataProvider createDocumentDataProvider
     */
    public function testCreateDocumentFromEvent(?string $name = null, ?string $description = null, ?string $fileDescriptorId = null, ?string $authorId = null, ?string $tags = null, ?string $eventId = null, ?string $exceptionClass = null, ?string $exceptionContains = null): void
    {
        if (! empty($exceptionClass)) {
            $this->expectException($exceptionClass);
            if (! empty($exceptionContains)) {
                $this->expectExceptionMessageMatches('/' . preg_quote($exceptionContains) . '/i');
            }
        }

        $fileDescriptorId = $fileDescriptorId ?? $this->file->getId();
        $name = $name ?? $this->faker->name;
        $description = $description ?? $this->faker->name;
        $authorId = $authorId ?? $this->author->getId();
        $tags = $tags ?? 'File Image work';
        $eventId = $eventId ?? $this->event->getId();

        $document = $this->createDocumentFromEvent->create($name, $description, $tags, $fileDescriptorId, $authorId, $eventId);

        if (! empty($exceptionClass)) {
            return;
        }

        $this->assertEquals($document->getName(), $name);
        $this->assertEquals($document->getVisibility(), DocumentEnum::PROTECTED_CODE);
        $this->assertEquals($document->getDescription(), $description);
        $this->assertEquals($document->getAuthor()->getId(), $authorId);
        $this->assertEquals($document->getFileDescriptor()->getId(), $fileDescriptorId);
        $events = $document->getEvents();
        $this->assertEquals($this->event->getId(), $events[0]->getId());
    }

    /**
     * @return mixed[]
     */
    public function createDocumentDataProvider(): array
    {
        $faker = Factory::create('fr_FR');

        return [
            'Create Document' => ['name' => 'Valid document'],
            'Create Document with wrong file id' => ['fileDescriptorId' => Uuid::uuid1()->toString()],
            'Create Document with wrong author id' => ['authorId' => Uuid::uuid1()->toString()],
        ];
    }
}
