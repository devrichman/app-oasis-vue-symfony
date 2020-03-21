<?php

declare(strict_types=1);

namespace App\Application\File;

use App\Domain\Exception\InvalidFileValue;
use App\Domain\Model\FileDescriptor;
use Psr\Http\Message\UploadedFileInterface;

final class UploadPicture
{
    private UploadFile $uploadFile;

    public function __construct(UploadFile $uploadFile)
    {
        $this->uploadFile = $uploadFile;
    }

    /**
     * @throws InvalidFileValue
     */
    public function upload(UploadedFileInterface $file, string $rootPath): FileDescriptor
    {
        InvalidFileValue::isPicture($file);

        return $this->uploadFile->upload($file, $rootPath);
    }
}
