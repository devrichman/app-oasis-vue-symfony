<?php

declare(strict_types=1);

namespace App\Tests\Application\File;

use App\Application\File\DeleteFile;
use App\Domain\Exception\NotFound;
use App\Domain\Model\FileDescriptor;
use App\Domain\Repository\FileDescriptorRepository;
use App\Tests\Application\ApplicationTestCase;
use Ramsey\Uuid\Uuid;
use function file_exists;
use function file_put_contents;
use function filesize;
use function getenv;
use function mkdir;
use function rmdir;
use function scandir;
use function str_repeat;
use function unlink;

class DeleteFileTest extends ApplicationTestCase
{
    protected DeleteFile $deleteFile;
    protected string $basedir;
    protected FileDescriptor $file;

    protected function setUp(): void
    {
        parent::setUp();
        $this->deleteFile = self::$container->get(DeleteFile::class);

        $this->basedir = getenv('ROOT_PATH') . 'public/files/test';
        if (! file_exists($this->basedir)) {
            mkdir($this->basedir);
        }
        file_put_contents($this->basedir . '/test.pdf', str_repeat('1212', 20480));

        $this->file = new FileDescriptor('test.pdf', filesize($this->basedir . '/test.pdf'), 'file:///public/files/test/test.pdf');
        self::$container->get(FileDescriptorRepository::class)->save($this->file);
    }

    protected function tearDown(): void
    {
        foreach (scandir($this->basedir) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            unlink($this->basedir . '/' . $file);
        }
        rmdir($this->basedir);

        parent::tearDown();
    }

    public function testUploadFile(): void
    {
        $this->expectException(NotFound::class);
        $this->deleteFile->delete(Uuid::uuid1()->toString());

        $this->deleteFile->delete($this->file->getId());
        $this->assertFalse(file_exists(getenv('ROOT_PATH') . 'public/files/test/test.pdf'));
        $this->expectException(NotFound::class);
        self::$container->get(FileDescriptorRepository::class)->mustFindOneById($this->file->getId());
    }
}
