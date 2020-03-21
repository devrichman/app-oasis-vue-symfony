<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Exception\NotFound;
use App\Domain\Model\FileDescriptor;

interface FileDescriptorRepository
{
    public function save(FileDescriptor $fileDescriptor): void;

    public function delete(FileDescriptor $fileDescriptor): void;

    /**
     * @throws NotFound
     */
    public function mustFindOneById(string $id): FileDescriptor;
}
