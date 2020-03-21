<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use GraphQL\Error\ClientAware;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
use function in_array;
use function pathinfo;
use function Safe\sprintf;
use function strtolower;

final class InvalidFileValue extends RuntimeException implements ClientAware, DomainException
{
    public const VALID_PICTURE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif'];

    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'invalid file value';
    }

    private static function getProperty(?string $property = null): string
    {
        return $property ? $property : 'value';
    }

    /**
     * @throws self
     */
    public static function isNotEmpty(UploadedFileInterface $file, ?string $property = null): void
    {
        if ($file->getSize() === 0 || $file->getStream()->getSize() === 0) {
            throw new self(sprintf('Upload failed for %s', self::getProperty($property)), 400);
        }
    }

    /**
     * @throws self
     */
    public static function isPicture(UploadedFileInterface $file, ?string $property = null): void
    {
        if ($file->getClientFilename() === null) {
            throw new self(sprintf('The uploaded file for %s is not an image', self::getProperty($property)), 400);
        }

        $filename = pathinfo($file->getClientFilename());

        if (empty($filename['extension']) || ! in_array(strtolower($filename['extension']), self::VALID_PICTURE_EXTENSIONS)) {
            throw new self(sprintf('The upload file for %s is not an image', self::getProperty($property)), 400);
        }
    }
}
