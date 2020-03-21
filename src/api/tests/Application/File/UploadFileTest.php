<?php

declare(strict_types=1);

namespace App\Tests\Application\File;

use App\Application\File\UploadFile;
use App\Domain\Exception\InvalidFileValue;
use App\Tests\Application\ApplicationTestCase;
use Zend\Diactoros\UploadedFile;
use function file_exists;
use function file_put_contents;
use function filesize;
use function getenv;
use function mkdir;
use function rmdir;
use function scandir;
use function str_repeat;
use function unlink;
use const UPLOAD_ERR_OK;

class UploadFileTest extends ApplicationTestCase
{
    protected UploadFile $uploadFile;
    protected string $basedir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uploadFile = self::$container->get(UploadFile::class);

        $this->basedir = getenv('ROOT_PATH') . 'public/files/test';
        if (! file_exists($this->basedir)) {
            mkdir($this->basedir);
        }
        file_put_contents($this->basedir . '/test.pdf', str_repeat('1212', 20480));
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
        $file = new UploadedFile($this->basedir . '/test.pdf', filesize($this->basedir . '/test.pdf'), UPLOAD_ERR_OK, 'upload.pdf');
        $fileDescriptor = $this->uploadFile->upload($file, getenv('ROOT_PATH'));
        $this->assertEquals(filesize($this->basedir . '/test.pdf'), $fileDescriptor->getSize());
        $this->assertEquals('upload.pdf', $fileDescriptor->getName());
        $this->assertStringStartsWith('file://', $fileDescriptor->getUpstream());
        $this->assertStringEndsWith('.pdf', $fileDescriptor->getUpstream());

        $file = new UploadedFile('invalid path', 0, UPLOAD_ERR_OK);
        $this->expectException(InvalidFileValue::class);
        $this->uploadFile->upload($file, getenv('ROOT_PATH'));
    }
}
