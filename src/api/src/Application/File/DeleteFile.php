<?php

declare(strict_types=1);

namespace App\Application\File;

use App\Domain\Exception\NotFound;
use App\Domain\Repository\FileDescriptorRepository;
use function file_exists;
use function getenv;
use function Safe\parse_url;
use function Safe\unlink;

final class DeleteFile
{
    private FileDescriptorRepository $fileDescriptorRepository;

    public function __construct(FileDescriptorRepository $fileDescriptorRepository)
    {
        $this->fileDescriptorRepository = $fileDescriptorRepository;
    }

    /**
     * @throws NotFound
     */
    public function delete(string $fileDescriptorId): void
    {
        $fileDescriptor = $this->fileDescriptorRepository->mustFindOneById($fileDescriptorId);

        $upstream = parse_url($fileDescriptor->getUpstream());
        if ($upstream['scheme'] === 'file' && empty($upstream['host'])) {
            $filepath = getenv('ROOT_PATH') . $upstream['path'];
            if (file_exists($filepath)) {
                unlink(getenv('ROOT_PATH') . $upstream['path']);
            }
        }

        $this->fileDescriptorRepository->delete($fileDescriptor);
    }
}
