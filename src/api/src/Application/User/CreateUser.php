<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Enum\CivilityEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\Exist;
use App\Domain\Exception\InvalidArrayValue;
use App\Domain\Exception\InvalidRight;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\ResetPasswordToken;
use App\Domain\Model\User;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\FileDescriptorRepository;
use App\Domain\Repository\ResetPasswordTokenRepository;
use App\Domain\Repository\RoleRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use Ramsey\Uuid\Uuid;
use Safe\DateTimeImmutable;

final class CreateUser
{
    private UserRepository $userRepository;
    private UserTypeRepository $userTypeRepository;
    private CompanyRepository $companyRepository;
    private FileDescriptorRepository $fileDescriptorRepository;
    private RoleRepository $roleRepository;
    private CreateUserNotifier $createUserNotifier;
    private ResetPasswordTokenRepository $resetPasswordTokenRepository;

    public function __construct(
        UserRepository $userRepository,
        UserTypeRepository $userTypeRepository,
        CompanyRepository $companyRepository,
        FileDescriptorRepository $fileDescriptorRepository,
        RoleRepository $roleRepository,
        CreateUserNotifier $createUserNotifier,
        ResetPasswordTokenRepository $resetPasswordTokenRepository
    ) {
        $this->userRepository = $userRepository;
        $this->createUserNotifier = $createUserNotifier;
        $this->userTypeRepository = $userTypeRepository;
        $this->companyRepository = $companyRepository;
        $this->fileDescriptorRepository = $fileDescriptorRepository;
        $this->roleRepository = $roleRepository;
        $this->resetPasswordTokenRepository = $resetPasswordTokenRepository;
    }

    /**
     * @param string[] $roleIds
     *
     * @throws InvalidArrayValue
     * @throws InvalidStringValue
     * @throws InvalidRight
     * @throws Exist
     * @throws NotFound
     */
    public function create(
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
        bool $status = true,
        bool $save = true
    ): User {
        if (! $this->userRepository->checkEmailUnique($email)) {
            throw new Exist(User::class, [], true);
        }

        if ($typeId === UserTypeEnum::ADMINISTRATOR && $this->userRepository->getLoggedUser()->getType()->getId() !== UserTypeEnum::ADMINISTRATOR) {
            throw new InvalidRight();
        }

        $userType = $this->userTypeRepository->mustFindOneById($typeId);
        $user = new User($userType, $firstName, $lastName, $email, $phone);

        $roles = [];
        foreach ($roleIds as $roleId) {
            $roles[] = $this->roleRepository->mustFindOneById($roleId);
        }

        if (empty($roles)) {
            throw new InvalidArrayValue('Roles are required', 400);
        }

        $user->setProfilePicture(! empty($profilePictureId) ? $this->fileDescriptorRepository->mustFindOneById($profilePictureId) : null);
        $user->setRolesByUsersRoles($roles);
        $user->setStatus($status);
        $user->setCivility($civility ?? CivilityEnum::MISTER_CODE);
        $user->setAddress($address);
        $user->setLinkedin($linkedin);
        $user->setFunction($function);
        $user->setPreviousFunction($previousFunction);
        $user->setSeniorityDate($seniorityDate !== null ? new DateTimeImmutable($seniorityDate) : null);

        $coach = null;
        if ($typeId === UserTypeEnum::CANDIDATE) {
            if ($coachId === null) {
                throw new NotFound(User::class, ['coachId' => null]);
            }
            $coach = $this->userRepository->mustFindOneById($coachId);
            $user->setCoach($coach);
        }

        if ($companyId !== null) {
            $company = $this->companyRepository->mustFindOneById($companyId);
            $user->setCompany($company);
        }

        if ($save) {
            $this->userRepository->save($user);
        }

        $accessToken = Uuid::uuid1()->toString();
        $tokenPassword = $this->resetPasswordTokenRepository->encodeToken($user, $accessToken);
        $resetPasswordToken = new ResetPasswordToken($user, $accessToken);
        $this->resetPasswordTokenRepository->save($resetPasswordToken);

        $this->createUserNotifier->notify($user, $tokenPassword, $coach);

        return $user;
    }
}
