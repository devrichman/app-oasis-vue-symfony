<?php

declare(strict_types=1);

namespace App\Tests\Application\File;

use App\Application\File\UploadPicture;
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

class UploadPictureTest extends ApplicationTestCase
{
    protected UploadPicture $uploadPicture;
    protected string $basedir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uploadPicture = self::$container->get(UploadPicture::class);

        $this->basedir = getenv('ROOT_PATH') . 'public/files/test';
        if (! file_exists($this->basedir)) {
            mkdir($this->basedir);
        }
        file_put_contents($this->basedir . '/test.jpeg', str_repeat('1212', 20480));
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
        $file = new UploadedFile($this->basedir . '/test.jpeg', filesize($this->basedir . '/test.jpeg'), UPLOAD_ERR_OK, 'upload.jpeg');
        $fileDescriptor = $this->uploadPicture->upload($file, getenv('ROOT_PATH'));
        $this->assertEquals(filesize($this->basedir . '/test.jpeg'), $fileDescriptor->getSize());
        $this->assertEquals('upload.jpeg', $fileDescriptor->getName());
        $this->assertStringStartsWith('file://', $fileDescriptor->getUpstream());
        $this->assertStringEndsWith('.jpeg', $fileDescriptor->getUpstream());

        $file = new UploadedFile($this->basedir . '/test.jpeg', filesize($this->basedir . '/test.jpeg'), UPLOAD_ERR_OK, 'upload.pdf');
        $this->expectException(InvalidFileValue::class);
        $this->uploadPicture->upload($file, getenv('ROOT_PATH'));
    }
}
