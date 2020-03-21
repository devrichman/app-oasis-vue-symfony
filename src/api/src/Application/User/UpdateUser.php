<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Application\File\DeleteFile;
use App\Domain\Enum\CivilityEnum;
use App\Domain\Exception\Exist;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\FileDescriptorRepository;
use App\Domain\Repository\RoleRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;

final class UpdateUser
{
    private UserRepository $userRepository;
    private UpdateEmail $updateEmailAddress;
    private UserTypeRepository $userTypeRepository;
    private CompanyRepository $companyRepository;
    private FileDescriptorRepository $fileDescriptorRepository;
    private RoleRepository $roleRepository;
    private DeleteFile $deleteFile;

    public function __construct(UserRepository $userRepository, UpdateEmail $updateEmailAddress, UserTypeRepository $userTypeRepository, CompanyRepository $companyRepository, FileDescriptorRepository $fileDescriptorRepository, RoleRepository $roleRepository, DeleteFile $deleteFile)
    {
        $this->userRepository = $userRepository;
        $this->updateEmailAddress = $updateEmailAddress;
        $this->userTypeRepository = $userTypeRepository;
        $this->companyRepository = $companyRepository;
        $this->fileDescriptorRepository = $fileDescriptorRepository;
        $this->roleRepository = $roleRepository;
        $this->deleteFile = $deleteFile;
    }

    /**
     * @param string[] $roleIds
     *
     * @throws InvalidStringValue
     * @throws NotFound
     * @throws Exist
     */
    public function updateUser(
        string $id,
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $typeId,
        array $roleIds,
        string $civility = CivilityEnum::MISTER_CODE,
        ?string $address = null,
        ?string $linkedin = null,
        ?string $function = null,
        ?string $seniorityDate = null,
        ?string $previousFunction = null,
        ?string $companyId = null,
        ?string $coachId = null,
        ?string $profilePictureId = null,
        bool $status = true
    ): User {
        $user = $this->userRepository->mustFindOneById($id);
        if ($user->getEmail() !== $email) {
            $this->updateEmailAddress->updateEmail($user->getId(), $email);
        }

        $user->setCoach(! empty($coachId) ? $this->userRepository->mustFindOneById($coachId) : null);
        $user->setType($this->userTypeRepository->mustFindOneById($typeId));
        $user->setCompany(! empty($companyId) ? $this->companyRepository->mustFindOneById($companyId) : null);
        $user->setStatus($status);

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

        $roles = [];
        foreach ($roleIds as $roleId) {
            $roles[] = $this->roleRepository->mustFindOneById($roleId);
        }

        $user->setRolesByUsersRoles($roles);
        $this->userRepository->save($user);

        if (! empty($deleteFile)) {
            $this->deleteFile->delete($deleteFile);
        }

        return $user;
    }
}
