<?php

declare(strict_types=1);

namespace App\Tests\Application\Company;

use App\Application\Document\DeleteDocument;
use App\Domain\Enum\DocumentEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Model\Document;
use App\Domain\Model\FileDescriptor;
use App\Domain\Model\User;
use App\Domain\Repository\DocumentRepository;
use App\Domain\Repository\FileDescriptorRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use App\Tests\Application\ApplicationTestCase;
use Faker\Factory;
use function preg_quote;

class DeleteDocumentTest extends ApplicationTestCase
{
    protected DeleteDocument $deleteDocument;
    protected FileDescriptor $file;
    protected Document $document;
    protected User $author;

    protected function setUp(): void
    {
        parent::setUp();

        $userType = self::$container->get(UserTypeRepository::class)->mustFindOneById(UserTypeEnum::ADMINISTRATOR);
        $this->file = new FileDescriptor($this->faker->name, $this->faker->numberBetween(1500, 25000), $this->faker->name);
        $this->author = new User($userType, $this->faker->firstName, $this->faker->lastName, $this->faker->email, $this->faker->phoneNumber);
        $this->document = new Document($this->file, $this->author, $this->faker->name, $this->faker->text, $this->faker->text, DocumentEnum::PUBLIC_CODE);
        $this->deleteDocument = self::$container->get(DeleteDocument::class);

        self::$container->get(UserRepository::class)->save($this->author);
        self::$container->get(DocumentRepository::class)->save($this->document);
        self::$container->get(FileDescriptorRepository::class)->save($this->file);
    }

    /**
     * @param mixed[] $deleteData
     *
     * @dataProvider createCompanyDataProvider
     */
    public function testDeleteDocument(?array $deleteData = [], ?string $exceptionClass = null, ?string $exceptionContains = null): void
    {
        if (! empty($exceptionClass)) {
            $this->expectException($exceptionClass);
            if (! empty($exceptionContains)) {
                $this->expectExceptionMessageMatches('/' . preg_quote($exceptionContains) . '/i');
            }
        }

        $document = $this->deleteDocument->delete(
            $deleteData['id'] ?? $this->document->getId(),
        );

        if (! empty($exceptionClass)) {
            return;
        }

        $this->assertTrue($document->getDeleted());
    }

    /**
     * @return mixed[]
     */
    public function createCompanyDataProvider(): array
    {
        $faker = Factory::create('fr_FR');

        return [
            'Delete Document' => [],
        ];
    }
}
