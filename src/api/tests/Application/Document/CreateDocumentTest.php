<?php

declare(strict_types=1);

namespace App\Tests\Application\Company;

use App\Application\Document\CreateDocument;
use App\Domain\Enum\DocumentEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Model\FileDescriptor;
use App\Domain\Model\User;
use App\Domain\Repository\FileDescriptorRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use App\Tests\Application\ApplicationTestCase;
use Faker\Factory;
use Ramsey\Uuid\Uuid;
use function preg_quote;

class CreateDocumentTest extends ApplicationTestCase
{
    protected CreateDocument $createDocument;
    protected FileDescriptor $file;
    protected User $author;

    protected function setUp(): void
    {
        parent::setUp();

        $userType = self::$container->get(UserTypeRepository::class)->mustFindOneById(UserTypeEnum::COACH);
        $this->createDocument = self::$container->get(CreateDocument::class);
        $this->file = new FileDescriptor($this->faker->name, $this->faker->numberBetween(1500, 25000), $this->faker->name);
        $this->author = new User($userType, $this->faker->firstName, $this->faker->lastName, $this->faker->email, $this->faker->phoneNumber);

        self::$container->get(UserRepository::class)->save($this->author);
        self::$container->get(FileDescriptorRepository::class)->save($this->file);
    }

    /**
     * @dataProvider createCompanyDataProvider
     */
    public function testCreateDocument(?string $name = null, ?string $description = null, ?string $fileDescriptorId = null, ?string $authorId = null, ?string $tags = null, ?string $visibility = null, ?string $exceptionClass = null, ?string $exceptionContains = null): void
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
        $visibility = $visibility ?? DocumentEnum::PUBLIC_CODE;

        $document = $this->createDocument->create($name, $description, $tags, $visibility, $fileDescriptorId, $authorId);

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
            'Create Document' => ['name' => 'Valid document'],
            'Create Document with wrong file id' => ['fileDescriptorId' => Uuid::uuid1()->toString()],
            'Create Document with wrong author id' => ['authorId' => Uuid::uuid1()->toString()],
        ];
    }
}
