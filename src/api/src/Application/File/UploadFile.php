<?php

declare(strict_types=1);

namespace App\Application\File;

use App\Domain\Exception\InvalidFileValue;
use App\Domain\Model\FileDescriptor;
use App\Domain\Repository\FileDescriptorRepository;
use Psr\Http\Message\UploadedFileInterface;
use function file_exists;
use function hash;
use function mt_rand;
use function pathinfo;
use function Safe\filesize;

final class UploadFile
{
    private FileDescriptorRepository $fileDescriptorRepository;

    public function __construct(FileDescriptorRepository $fileDescriptorRepository)
    {
        $this->fileDescriptorRepository = $fileDescriptorRepository;
    }

    /**
     * @throws InvalidFileValue
     */
    public function upload(UploadedFileInterface $file, string $rootPath): FileDescriptor
    {
        // In case upload failed, or the file was larger than allowed by POST_MAX_SIZE
        InvalidFileValue::isNotEmpty($file, 'file');

        if (empty($file->getClientFilename())) {
            throw new InvalidFileValue('File uploaded without a name', 400);
        }

        $filename = pathinfo($file->getClientFilename());
        $upstream = 'public/files/' . hash('sha256', (string) mt_rand()) . (isset($filename['extension']) ? '.' . $filename['extension'] : '');
        $path = $rootPath . $upstream;
        $file->moveTo($path);
        if (! file_exists($path) || filesize($path) === 0) {
            throw new InvalidFileValue('Upload failed', 400);
        }

        $fileDescriptor = new FileDescriptor($file->getClientFilename(), filesize($path), 'file:///' . $upstream);
        $this->fileDescriptorRepository->save($fileDescriptor);

        return $fileDescriptor;
    }
}
