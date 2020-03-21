<?php

declare(strict_types=1);

namespace App\Tests\Application\Company;

use App\Application\Document\UpdateDocument;
use App\Domain\Enum\DocumentEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Document;
use App\Domain\Model\FileDescriptor;
use App\Domain\Model\User;
use App\Domain\Repository\DocumentRepository;
use App\Domain\Repository\FileDescriptorRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use App\Tests\Application\ApplicationTestCase;
use Faker\Factory;
use Ramsey\Uuid\Uuid;
use function preg_quote;

class UpdateDocumentTest extends ApplicationTestCase
{
    protected UpdateDocument $updateDocument;
    protected FileDescriptor $file;
    protected Document $document;
    protected User $author;

    protected function setUp(): void
    {
        parent::setUp();

        $userType = self::$container->get(UserTypeRepository::class)->mustFindOneById(UserTypeEnum::ADMINISTRATOR);
        $this->updateDocument = self::$container->get(UpdateDocument::class);
        $this->file = new FileDescriptor($this->faker->name, $this->faker->numberBetween(1500, 25000), $this->faker->name);
        $this->author = new User($userType, $this->faker->firstName, $this->faker->lastName, $this->faker->email, $this->faker->phoneNumber);
        $this->document = new Document($this->file, $this->author, $this->faker->name, $this->faker->text, $this->faker->text, DocumentEnum::PUBLIC_CODE);

        self::$container->get(UserRepository::class)->save($this->author);
        self::$container->get(DocumentRepository::class)->save($this->document);
        self::$container->get(FileDescriptorRepository::class)->save($this->file);
    }

    /**
     * @param mixed[] $updateData
     *
     * @dataProvider createCompanyDataProvider
     */
    public function testUpdateDocument(array $updateData, ?string $exceptionClass = null, ?string $exceptionContains = null): void
    {
        if (! empty($exceptionClass)) {
            $this->expectException($exceptionClass);
            if (! empty($exceptionContains)) {
                $this->expectExceptionMessageMatches('/' . preg_quote($exceptionContains) . '/i');
            }
        }

        $name = $updateData['name'] ?? $this->faker->name;
        $description = $updateData['description'] ?? $this->faker->text;
        $tags = $updateData['tags'] ?? $this->faker->text;
        $visibility = $updateData['visibility'] ?? DocumentEnum::PUBLIC_CODE;
        $fileDescriptorId = $updateData['fileDescriptorId'] ?? $this->file->getId();
        $authorId = $updateData['authorId'] ?? $this->author->getId();

        $document = $this->updateDocument->update(
            $updateData['id'] ?? $this->document->getId(),
            $name,
            $description,
            $tags,
            $visibility,
            $fileDescriptorId,
            $authorId
        );

        if (! empty($exceptionClass)) {
            return;
        }

        $this->assertEquals($document->getName(), $name);
        $this->assertEquals($document->getVisibility(), $visibility);
        $this->assertEquals($document->getDescription(), $description);
        $this->assertEquals($document->getAuthor()->getId(), $authorId);
        $this->assertEquals($document->getFileDescriptor()->getId(), $fileDescriptorId);
    }

    /**
     * @return mixed[]
     */
    public function createCompanyDataProvider(): array
    {
        $faker = Factory::create('fr_FR');

        return [
            'Update Document' => [
                ['name' => 'Valid document'],
            ],
            'Update Document with wrong file id' => [
                ['fileDescriptorId' => Uuid::uuid1()->toString()],
                'exceptionClass' => NotFound::class,
                'exceptionContains' => FileDescriptor::class,
            ],
            'Update Document with wrong author id' => [
                ['authorId' => Uuid::uuid1()->toString()],
                'exceptionClass' => NotFound::class,
                'exceptionContains' => User::class,
            ],
        ];
    }
}
