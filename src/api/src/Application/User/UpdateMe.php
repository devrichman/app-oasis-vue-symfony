<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Application\File\DeleteFile;
use App\Domain\Enum\CivilityEnum;
use App\Domain\Exception\Exist;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use App\Domain\Repository\FileDescriptorRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;

final class UpdateMe
{
    private UserRepository $userRepository;
    private UpdateEmail $updateEmail;
    private UserTypeRepository $userTypeRepository;
    private FileDescriptorRepository $fileDescriptorRepository;
    private DeleteFile $deleteFile;

    public function __construct(UserRepository $userRepository, UpdateEmail $updateEmail, UserTypeRepository $userTypeRepository, FileDescriptorRepository $fileDescriptorRepository, DeleteFile $deleteFile)
    {
        $this->userRepository = $userRepository;
        $this->updateEmail = $updateEmail;
        $this->userTypeRepository = $userTypeRepository;
        $this->fileDescriptorRepository = $fileDescriptorRepository;
        $this->deleteFile = $deleteFile;
    }

    /**
     * @throws Exist
     * @throws InvalidStringValue
     * @throws NotFound
     */
    public function updateMe(
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $civility = CivilityEnum::MISTER_CODE,
        ?string $address = null,
        ?string $linkedin = null,
        ?string $function = null,
        ?string $seniorityDate = null,
        ?string $previousFunction = null,
        ?string $profilePictureId = null
    ): User {
        $user = $this->userRepository->getLoggedUser();

        if ($user->getEmail() !== $email) {
            $this->updateEmail->updateEmail($user->getId(), $email);
        }

        $deleteFile = null;
        if ($user->getProfilePicture() !== null && $profilePictureId !== $user->getProfilePicture()->getId()) {
            $deleteFile = $user->getProfilePicture()->getId();
        }

        $user->setUserInformation(
            $user,
            $firstName,
            $lastName,
            $phone,
            $civility,
            $address,
            $linkedin,
            $function,
            $seniorityDate,
            $previousFunction,
            ! empty($profilePictureId) ? $this->fileDescriptorRepository->mustFindOneById($profilePictureId) : null
        );

        $this->userRepository->save($user);

        if (! empty($deleteFile)) {
            $this->deleteFile->delete($deleteFile);
        }

        return $user;
    }
}
