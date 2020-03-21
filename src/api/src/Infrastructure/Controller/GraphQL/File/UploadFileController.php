<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\File;

use App\Application\File\UploadFile;
use App\Domain\Exception\InvalidFileValue;
use App\Domain\Model\FileDescriptor;
use App\Infrastructure\Config\EnvVarHelper;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class UploadFileController extends AbstractController
{
    private UploadFile $uploadFile;
    private EnvVarHelper $envVarHelper;

    public function __construct(UploadFile $uploadFile, EnvVarHelper $envVarHelper)
    {
        $this->uploadFile = $uploadFile;
        $this->envVarHelper = $envVarHelper;
    }

    /**
     * @throws InvalidFileValue
     *
     * @Mutation
     */
    public function uploadFile(UploadedFileInterface $file): FileDescriptor
    {
        return $this->uploadFile->upload($file, $this->envVarHelper->fetch('ROOT_PATH'));
    }
}
