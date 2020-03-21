<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\InvalidImportUser;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Company;
use App\Domain\Model\ResetPasswordToken;
use App\Domain\Model\Role;
use App\Domain\Model\User;
use App\Domain\Model\UserType;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\ResetPasswordTokenRepository;
use App\Domain\Repository\RoleRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use Ramsey\Uuid\Uuid;
use Safe\DateTimeImmutable;
use function Safe\sprintf;
use function strtolower;
use function trim;

final class CreateUserFromImport
{
    private UserRepository $userRepository;
    private UserTypeRepository $userTypeRepository;
    private CompanyRepository $companyRepository;
    private RoleRepository $roleRepository;
    private CreateUserNotifier $createUserNotifier;
    private ResetPasswordTokenRepository $resetPasswordTokenRepository;

    private const MODEL_MAP = [
        Role::class => 'Le rôle que vous avez entré n\'existe pas',
        User::class => 'Le coach référent que vous avez entré n\'existe pas',
        Company::class => 'L\'entreprise que vous avez entré n\'existe pas',
    ];

    private const ERROR_MAP = [
        'type' => 'Le champ Type est obligatoire',
        'firstName' => 'Le champ Prénom est obligatoire',
        'lastName' => 'Le champ Nom est obligatoire',
        'email' => 'Le format de l\'email est invalide',
        'required_email' => 'L\'email existe déjà',
        'duplicate_email' => 'L\'utilisateur avec l\'e-mail %s existe déjà',
        'phone' => 'Le numéro de téléphone est dans un format invalide',
        'required_phone' => 'Le champ Téléphone est obligatoire',
        'roles' => 'Au moins un rôle doit être sélectionné',
        'civility' => 'La civilité saisie n\'est pas valide',
    ];

    /** @var Role[] */
    private static array $cachedRoles = [];
    /** @var User[] */
    private static array $cachedUsers = [];
    /** @var Company[] */
    private static array $cachedCompanies = [];
    /** @var UserType[] */
    private static array $cachedUserTypes = [];
    private static ?User $cachedLoggedUser = null;

    public function __construct(
        UserRepository $userRepository,
        UserTypeRepository $userTypeRepository,
        CompanyRepository $companyRepository,
        RoleRepository $roleRepository,
        CreateUserNotifier $createUserNotifier,
        ResetPasswordTokenRepository $resetPasswordTokenRepository
    ) {
        $this->userRepository = $userRepository;
        $this->userTypeRepository = $userTypeRepository;
        $this->companyRepository = $companyRepository;
        $this->roleRepository = $roleRepository;
        $this->createUserNotifier = $createUserNotifier;
        $this->resetPasswordTokenRepository = $resetPasswordTokenRepository;
    }

    /**
     * @param mixed[] $row
     *
     * @throws InvalidStringValue
     */
    public function import(array $row, bool $save = true): ?User
    {
        $errors = [];
        if (! $save && ! $this->userRepository->checkEmailUnique($row['email'])) {
            $errors[] = sprintf(self::ERROR_MAP['duplicate_email'], $row['email']);
        }

        try {
            User::validateFirstName($row['firstName']);
        } catch (InvalidStringValue $e) {
            $errors[] = self::ERROR_MAP['firstName'];
        }
        if (isset(UserTypeEnum::values()[$row['type']])) {
            $row['type'] = UserTypeEnum::values()[$row['type']];
        }

        try {
            User::validateLastName($row['lastName']);
        } catch (InvalidStringValue $e) {
            $errors[] = self::ERROR_MAP['lastName'];
        }

        try {
            User::validateEmail($row['email']);
        } catch (InvalidStringValue $e) {
            $errors[] = empty($row['email']) ? self::ERROR_MAP['email'] : self::ERROR_MAP['required_email'];
        }

        try {
            User::validatePhone($row['phone']);
        } catch (InvalidStringValue $e) {
            $errors[] = empty($row['phone']) ? self::ERROR_MAP['required_phone'] : self::ERROR_MAP['phone'];
        }

        try {
            User::validateCivility($row['civility']);
        } catch (InvalidStringValue $e) {
            $errors[] = self::ERROR_MAP['civility'];
        }

        /** @var Role[] $roles */
        $roles = [];
        if (empty($row['roles'])) {
            $errors[] = self::ERROR_MAP['roles'];
        }
        foreach ($row['roles'] as $k => $roleName) {
            if (isset(self::$cachedRoles[trim($roleName)])) {
                $roles[] = self::$cachedRoles[trim($roleName)];
            } else {
                $role = $this->roleRepository->findOneByName(trim($roleName));
                if ($role === null) {
                    $errors[] = self::MODEL_MAP[Role::class];
                } else {
                    $roles[] = $role;
                    self::$cachedRoles[trim($roleName)] = $role;
                }
            }
        }

        $company = null;
        $coach = null;
        if ($row['type'] === UserTypeEnum::CANDIDATE) {
            if (isset(self::$cachedCompanies[$row['company']])) {
                $company = self::$cachedCompanies[$row['company']];
            } else {
                $company = $this->companyRepository->findOneByName($row['company']);
                if ($company === null) {
                    $errors[] = self::MODEL_MAP[Company::class];
                } else {
                    self::$cachedCompanies[$row['company']] = $company;
                }
            }

            if (isset(self::$cachedUsers[$row['coach']])) {
                $coach = self::$cachedUsers[$row['coach']];
            } else {
                $coach = $this->userRepository->findOneByEmail($row['coach']);
                if ($coach === null) {
                    $errors[] = self::MODEL_MAP[User::class];
                } else {
                    self::$cachedUsers[$row['coach']] = $coach;
                }
            }
        }

        if (! isset(self::$cachedUserTypes[$row['type']])) {
            try {
                self::$cachedUserTypes[$row['type']] = $this->userTypeRepository->mustFindOneById($row['type']);
            } catch (NotFound $e) {
                $errors[] = self::ERROR_MAP['type'];
            }
        }

        if (! empty($errors)) {
            throw new InvalidImportUser($errors);
        }

        $user = null;
        if ($save) {
            if (self::$cachedLoggedUser === null) {
                self::$cachedLoggedUser = $this->userRepository->getLoggedUser();
            }

            $user = new User(self::$cachedUserTypes[$row['type']], $row['firstName'], $row['lastName'], $row['email'], $row['phone']);
            $user->setRolesByUsersRoles($roles);
            $user->setStatus(strtolower($row['status']) === 'oui');
            $user->setCivility($row['civility']);
            $user->setAddress($row['address']);
            $user->setLinkedin($row['linkedIn']);
            $user->setFunction($row['function']);
            $user->setPreviousFunction($row['previousFunction']);
            $user->setSeniorityDate($row['seniorityDate'] ? new DateTimeImmutable($row['seniorityDate']) : null);
            $user->setCreatedAt(new DateTimeImmutable());
            $user->setCreatedBy(self::$cachedLoggedUser);

            if ($row['type'] === UserTypeEnum::CANDIDATE) {
                $user->setCoach($coach);
                $user->setCompany($company);
            }

            $this->userRepository->saveNoLog($user);

            $accessToken = Uuid::uuid1()->toString();
            $tokenPassword = $this->resetPasswordTokenRepository->encodeToken($user, $accessToken);
            $resetPasswordToken = new ResetPasswordToken($user, $accessToken);
            $this->resetPasswordTokenRepository->save($resetPasswordToken);

            $this->createUserNotifier->notify($user, $tokenPassword);
        }

        return $user;
    }
}
