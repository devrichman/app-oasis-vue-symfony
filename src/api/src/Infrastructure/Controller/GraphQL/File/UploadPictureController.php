<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\File;

use App\Application\File\UploadPicture;
use App\Domain\Exception\InvalidFileValue;
use App\Domain\Model\FileDescriptor;
use App\Infrastructure\Config\EnvVarHelper;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class UploadPictureController extends AbstractController
{
    private UploadPicture $uploadPicture;
    private EnvVarHelper $envVarHelper;

    public function __construct(UploadPicture $uploadPicture, EnvVarHelper $envVarHelper)
    {
        $this->uploadPicture = $uploadPicture;
        $this->envVarHelper = $envVarHelper;
    }

    /**
     * @throws InvalidFileValue
     *
     * @Mutation
     */
    public function uploadPicture(UploadedFileInterface $file): FileDescriptor
    {
        return $this->uploadPicture->upload($file, $this->envVarHelper->fetch('ROOT_PATH'));
    }
}
